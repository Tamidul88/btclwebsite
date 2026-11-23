<?php

namespace App\Support;

/**
 * Minimal IPv4/IPv6 CIDR matcher. Accepts a bare address or "addr/prefix".
 */
class Cidr
{
    public static function matches(?string $ip, string $range): bool
    {
        if ($ip === null || $ip === '') {
            return false;
        }

        $range = trim($range);

        if ($range === '') {
            return false;
        }

        if (! str_contains($range, '/')) {
            return hash_equals($range, $ip);
        }

        [$subnet, $bits] = explode('/', $range, 2);

        if (! is_numeric($bits)) {
            return false;
        }

        $bits = (int) $bits;
        $ipBin = @inet_pton($ip);
        $subnetBin = @inet_pton($subnet);

        // Different families (v4 vs v6) can never match.
        if ($ipBin === false || $subnetBin === false || strlen($ipBin) !== strlen($subnetBin)) {
            return false;
        }

        $maxBits = strlen($ipBin) * 8;

        if ($bits < 0 || $bits > $maxBits) {
            return false;
        }

        $wholeBytes = intdiv($bits, 8);
        $remainder = $bits % 8;

        if ($wholeBytes > 0 && ! hash_equals(substr($subnetBin, 0, $wholeBytes), substr($ipBin, 0, $wholeBytes))) {
            return false;
        }

        if ($remainder === 0) {
            return true;
        }

        $mask = ~((1 << (8 - $remainder)) - 1) & 0xFF;

        return (ord($ipBin[$wholeBytes]) & $mask) === (ord($subnetBin[$wholeBytes]) & $mask);
    }
}
