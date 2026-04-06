<?php

namespace App\Services;

class TotpService
{
    public const DEFAULT_DIGITS = 6;
    public const DEFAULT_PERIOD = 30;

    public function generateSecret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $secret;
    }

    public function getProvisioningUri(string $label, string $secret, string $issuer): string
    {
        $encodedLabel = rawurlencode($label);
        $encodedIssuer = rawurlencode($issuer);

        return "otpauth://totp/{$encodedIssuer}:{$encodedLabel}?secret={$secret}&issuer={$encodedIssuer}&algorithm=SHA1&digits=" . self::DEFAULT_DIGITS . '&period=' . self::DEFAULT_PERIOD;
    }

    public function verifyCode(string $secret, string $code, int $window = 1, ?int $timestamp = null): bool
    {
        $code = preg_replace('/\s+/', '', (string) $code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timestamp = $timestamp ?? time();
        $counter = (int) floor($timestamp / self::DEFAULT_PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->generateHotp($secret, $counter + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public function getQrCodeUrl(string $otpauthUri, int $size = 220): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($otpauthUri);
    }

    private function generateHotp(string $secret, int $counter): string
    {
        $secretBytes = $this->base32Decode($secret);
        if ($secretBytes === null) {
            return str_repeat('0', self::DEFAULT_DIGITS);
        }

        $binaryCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $secretBytes, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);
        $value = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;
        $modulo = 10 ** self::DEFAULT_DIGITS;

        return str_pad((string) ($value % $modulo), self::DEFAULT_DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $base32): ?string
    {
        $base32 = strtoupper($base32);
        $base32 = preg_replace('/[^A-Z2-7]/', '', $base32);

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        for ($i = 0, $len = strlen($base32); $i < $len; $i++) {
            $char = $base32[$i];
            $value = strpos($alphabet, $char);
            if ($value === false) {
                return null;
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            while ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}
