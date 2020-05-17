<?php

declare(strict_types = 1);

namespace App\Service;

use Exception;
use Generator;
use App\Entity\Media;
use RuntimeException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class MediaService
{
    const SECRET_KEY_LENGHT = 256;

    private string                $mediaFolder;

    private Filesystem            $filesystem;

    private FileEncryptionService $fileEncryption;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    public function __construct(
        EncryptionService $encryptionService,
        FileEncryptionService $fileEncryption,
        string $mediaFolder
    ) {
        $this->encryptionService = $encryptionService;
        $this->mediaFolder       = $mediaFolder;
        $this->fileEncryption    = $fileEncryption;
        $this->filesystem        = new Filesystem();
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

        $password          = $this->generatePassword(self::SECRET_KEY_LENGHT);
        $encryptedPassword = $this->encryptionService->encryptData($password);

        $media
            ->setUuid(Uuid::uuid4())
            ->setContentType($file->getClientMimeType())
            ->setSize($file->getSize())
            ->setExtension($file->guessExtension())
            ->setKey(base64_encode($encryptedPassword));

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
        $this->fileEncryption->encryptFile($file, $password, $path);

        // remove uploaded file
        $this
            ->filesystem
            ->remove($file->getRealPath());

        return realpath($path);
    }

    /**
     * @throws FileNotFoundException
     */
    public function decrypt(Media $media): Generator
    {
        $path = $this->absolutePath($media);

        $key = $this
            ->encryptionService
            ->decryptData(base64_decode($media->getKey()));

        yield from $this
            ->fileEncryption
            ->decryptFile(new File($path, true), $key);
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

    private function generatePassword(int $length): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
                 '0123456789-=~!@#$%^&*()_+,.<>?;:[]{}';

        $password = '';
        $max      = strlen($chars) - 1;

        for ($index = 0; $index < $length; $index ++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}
