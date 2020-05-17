<?php

declare(strict_types = 1);

namespace App\Service;

use Generator;
use Symfony\Component\HttpFoundation\File\File;

class FileEncryptionService
{
    public const ENCRYPT_BLOCK_SIZE = 512;

    /**
     * Must be lower than encryption block
     */
    public const FILE_CHUNK_SIZE = 450;

    private EncryptionService $encryptionService;

    public function __construct(
        EncryptionService $encryptionService
    ) {
        $this->encryptionService = $encryptionService;
    }

    public function encryptFile(File $file, string $destination): File
    {
        $source    = fopen($file->getRealPath(), 'rb');
        $encrypted = fopen($destination, 'wb');

        while ($chunk = fread($source, self::FILE_CHUNK_SIZE)) {
            $block = $this->encryptionService->encryptData($chunk);
            fwrite($encrypted, $block);
        }

        fclose($source);
        fclose($encrypted);

        return new File($destination, true);
    }

    public function decryptFile(File $encrypted): Generator
    {
        $source = fopen($encrypted->getRealPath(), 'rb');

        while ($block = fread($source, self::ENCRYPT_BLOCK_SIZE)) {
            if ($block) {
                yield $this->encryptionService->decryptData($block);
            }
        }

        fclose($source);
    }
}
