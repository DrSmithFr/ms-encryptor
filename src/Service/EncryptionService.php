<?php

declare(strict_types = 1);

namespace App\Service;

class EncryptionService
{
    private string $publicKey;

    private string $secretKey;

    public function __construct(
        string $publicKey,
        string $secretKey
    ) {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }

    public function encryptData(string $cipher): string
    {
        openssl_public_encrypt($cipher, $encrypted, $this->getPublicKey());
        return $encrypted;
    }

    public function decryptData(string $encrypted): string
    {
        openssl_private_decrypt($encrypted, $cipher, $this->getPrivateKey());
        return $cipher;
    }

    private function getPrivateKey(): string
    {
        return trim(file_get_contents($this->secretKey));
    }

    private function getPublicKey(): string
    {
        return trim(file_get_contents($this->publicKey));
    }
}
