<?php

declare(strict_types = 1);

namespace App\Controller;

use Exception;
use App\Entity\Media;
use App\Form\MediaType;
use App\Service\MediaService;
use Swagger\Annotations as SWG;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route(path="/medias", name="medias_")
 * @IsGranted("ROLE_USER")
 */
class MediaController extends AbstractApiController
{
    /**
     * Return BinaryFileResponse according to media mineType (for download and display)
     * @Route("/{uuid}", name="by_id_file", methods={"GET"}, requirements={"id"="\d+"})
     * @ParamConverter("media", class="App\Entity\Media")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="Media uniq identifier",
     *     required=true,
     *     @SWG\Schema(type="number")
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The requested medias"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @SWG\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     * @param Media        $media
     * @param MediaService $mediaService
     *
     * @return BinaryFileResponse
     */
    public function getByIdAction(
        Media $media,
        MediaService $mediaService
    ): BinaryFileResponse {
        $file = $mediaService->getFile($media);
        return new BinaryFileResponse($file);
    }

    /**
     * Information about size, mineType and extension
     * @Route("/{uuid}/metadata", name="by_id_metadata", methods={"GET"}, requirements={"id"="\d+"})
     * @ParamConverter("media", class="App\Entity\Media")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="Media uuid identifier",
     *     required=true,
     *     @SWG\Schema(type="number")
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The requested medias",
     *     @Model(type=Media::class, groups={"id", "Default"})
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @SWG\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     * @param Media $media
     *
     * @return JsonResponse
     */
    public function getMetadataByIdAction(
        Media $media
    ): JsonResponse {
        return $this->serializeResponse($media, ['Default']);
    }

    /**
     * Upload an encrypt a new media (retrieve the UUID of newly created media)
     * @Route("", name="add", methods={"POST"})
     * @SWG\Parameter(
     *     name="json body",
     *     in="body",
     *     description="Json representation of a Media",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=Media::class, groups={"id", "Default"}))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The media has been created",
     *     @SWG\Schema(
     *        type="object",
     *        example={"uuid": "gjc7834ace3-8525-4814-bf0f-b7146bc9e8ab"}
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="The id submitted in body dont match the one on url"
     * )
     * @SWG\Response(
     *     response=406,
     *     description="No form submitted"
     * )
     * @SWG\Tag(name="Medias")
     * @Security(name="Bearer")
     *
     * @throws Exception
     *
     * @param EntityManagerInterface $entityManager
     * @param MediaService           $mediaService
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): JsonResponse {
        $media = new Media();

        $form = $this
            ->createForm(MediaType::class, $media)
            ->submit($request->files->all());

        if (!$form->isSubmitted()) {
            return $this->messageResponse(
                'No form submitted',
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        if (!$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        $mediaService->upload($media);

        $entityManager->persist($media);
        $entityManager->flush();

        return $this->json(
            [
                'uuid' => $media->getUuid()
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
