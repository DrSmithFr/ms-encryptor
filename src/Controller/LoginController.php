<?php

declare(strict_types = 1);

namespace App\Controller;

use RuntimeException;
use App\Model\LoginModel;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Controller\Traits\SerializerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    use SerializerAware;

    /**
     * ConnectionController constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    /**
     * Initialise sessions with encryption API (Token valid for 30s)
     * @Route(path="/login", methods={"POST"}, name="app_login")
     * @SWG\Tag(name="Authentification")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="json",
     *     @SWG\Schema(@Model(type=LoginModel::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User connected",
     *     @SWG\Schema(
     *        type="object",
     *        example={"token": "gjc7834ace3-8525-4814-bf0f-b7146bc9e8ab"}
     *     )
     * )
     */
    final public function login(): Response
    {
        throw new RuntimeException(
            'You may have screwed the firewall configuration, this function should not have been called.'
        );
    }
}
