<?php

namespace Core;

class Encryption
{
    private static function getKey(): string
    {
        $config = require __DIR__ . '/../config/app.php';
        $key = $config['encryption_key'];
        return hash('sha256', $key, true);
    }

    /**
     * Encrypt a value using AES-256-GCM
     * Returns [encrypted_data, iv, tag] as base64 strings
     */
    public static function encrypt(string $plaintext): array
    {
        $key = self::getKey();
        $iv = random_bytes(12); // 96-bit IV for GCM
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed.');
        }

        return [
            'data' => base64_encode($ciphertext),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
        ];
    }

    /**
     * Decrypt a value encrypted with AES-256-GCM
     */
    public static function decrypt(string $encryptedData, string $iv, string $tag): string
    {
        $key = self::getKey();

        $plaintext = openssl_decrypt(
            base64_decode($encryptedData),
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            base64_decode($iv),
            base64_decode($tag)
        );

        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed. Data may be corrupted or key is wrong.');
        }

        return $plaintext;
    }
}
