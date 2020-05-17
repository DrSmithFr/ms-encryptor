<?php

declare(strict_types = 1);

namespace App\Service;

use Exception;
use App\Entity\Media;
use RuntimeException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class MediaService
{
    private string     $mediaFolder;

    private Filesystem $filesystem;

    public function __construct(string $mediaFolder)
    {
        $this->mediaFolder = $mediaFolder;
        $this->filesystem  = new Filesystem();
    }

    /**
     * @throws Exception
     */
    public function upload(Media $media): ?string
    {
        $file = $media->getFile();

        if ($file === null) {
            return null;
        }

        $media
            ->setUuid(Uuid::uuid4())
            ->setContentType($file->getClientMimeType())
            ->setSize($file->getSize())
            ->setExtension($file->guessExtension());

        $path = $this->absolutePath($media);

        if ($this->filesystem->exists($path)) {
            // must never occur with Uuid as filename
            throw new RuntimeException('createUniqueFileName() doesnt work as expected');
        }

        // ensure upload directory exist
        if (!$this->filesystem->exists($this->mediaFolder)) {
            $this
                ->filesystem
                ->mkdir($this->mediaFolder);
        }

        // write document to filesystem
        $this
            ->filesystem
            ->copy($file->getRealPath(), $path);

        return realpath($path);
    }

    /**
     * @throws FileNotFoundException
     */
    public function getFile(Media $media): File
    {
        $path = $this->absolutePath($media);
        return new File($path, true);
    }

    private function absolutePath(Media $media): ?string
    {
        return sprintf(
            '%s%s%s',
            $this->mediaFolder,
            DIRECTORY_SEPARATOR,
            $media->getUuid()
        );
    }
}
