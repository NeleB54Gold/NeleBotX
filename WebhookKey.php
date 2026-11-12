<?php

final class NeleBotXWebhookKey {
    private const VERSION = 'v1';
    private const AAD = 'NeleBotX webhook key v1';

    public static function encrypt(string $token, string $password): string {
        if ($token === '' || $password === '') {
            throw new InvalidArgumentException('Token and webhook password must not be empty.');
        }
        if (!function_exists('openssl_encrypt')) {
            throw new RuntimeException('The OpenSSL PHP extension is required for webhook key encryption.');
        }

        $salt = random_bytes(16);
        $nonce = random_bytes(12);
        $key = hash_hkdf('sha256', $password, 32, self::AAD, $salt);
        $tag = '';
        $ciphertext = openssl_encrypt($token, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, self::AAD, 16);
        if ($ciphertext === false || strlen($tag) !== 16) {
            throw new RuntimeException('Unable to encrypt the webhook key.');
        }

        return self::VERSION . '.' . rtrim(strtr(base64_encode($salt . $nonce . $tag . $ciphertext), '+/', '-_'), '=');
    }

    public static function decrypt(string $value, string $password): ?string {
        if ($value === '' || $password === '' || !function_exists('openssl_decrypt')) {
            return null;
        }
        if (!str_starts_with($value, self::VERSION . '.')) {
            return null;
        }

        $encoded = substr($value, strlen(self::VERSION) + 1);
        if ($encoded === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $encoded)) {
            return null;
        }
        $remainder = strlen($encoded) % 4;
        if ($remainder !== 0) {
            $encoded .= str_repeat('=', 4 - $remainder);
        }
        $raw = base64_decode(strtr($encoded, '-_', '+/'), true);
        if ($raw === false || strlen($raw) < 44) {
            return null;
        }

        $salt = substr($raw, 0, 16);
        $nonce = substr($raw, 16, 12);
        $tag = substr($raw, 28, 16);
        $ciphertext = substr($raw, 44);
        $key = hash_hkdf('sha256', $password, 32, self::AAD, $salt);
        $token = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, self::AAD);

        return is_string($token) && $token !== '' ? $token : null;
    }
}
