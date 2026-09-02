<?php

namespace App\Services;

class JwtService
{
    private string $secret;
    private int $expirationTime;

    public function __construct()
    {
        $this->secret = getenv('JWT_SECRET');
        if (!$this->secret) {
            throw new \RuntimeException('JWT_SECRET environment variable is required. Please set it in your .env file.');
        }
        $this->expirationTime = 86400; // 24 hours
    }

    public function createToken(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expirationTime;

        $headerEncoded = $this->base64urlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $payloadEncoded = $this->base64urlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $message = $headerEncoded . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $message, $this->secret, true);
        $signatureEncoded = $this->base64urlEncode($signature);

        return $message . '.' . $signatureEncoded;
    }

    public function decodeToken(string $token): ?array
    {
        try {
            // parse token
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;  // Invalid format
            }

            list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

            // verify signature
            $message = $headerEncoded . '.' . $payloadEncoded;
            $signature = hash_hmac('sha256', $message, $this->secret, true);
            $expectedSignature = $this->base64urlEncode($signature);

            // timing safe
            if (!hash_equals($signatureEncoded, $expectedSignature)) {
                return null;  // signature mismatch
            }

            // decode payload
            $payload = json_decode($this->base64urlDecode($payloadEncoded), true);

            if (!is_array($payload)) {
                return null;  // invalid json
            }

            // check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return null;  // Token expired
            }

            return $payload;

        } catch (\Exception $e) {
            // invalid token
            return null;
        }
    }

    public static function extractTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;  // no auth header
    }

    private function base64urlEncode(string $data): string
    {
        $b64 = base64_encode($data);
        $urlSafe = strtr($b64, '+/', '-_');
        return rtrim($urlSafe, '=');
    }

    /**
     * Base64 URL-safe decoding
     * Reverses base64urlEncode
     */
    private function base64urlDecode(string $data): string
    {
        $padded = $data . str_repeat('=', strlen($data) % 4);
        $b64 = strtr($padded, '-_', '+/');
        return base64_decode($b64);
    }
}