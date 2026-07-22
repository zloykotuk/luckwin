<?php

namespace App\Helper;

use Propaganistas\LaravelPhone\PhoneNumber;

final class PhoneNormalizer
{
    /**
     * Normalize a phone number for storage: E.164 format without the leading plus,
     * i.e. country code immediately followed by the subscriber number.
     */
    public static function normalize(string $rawNumber): string
    {
        return ltrim((new PhoneNumber($rawNumber))->formatE164(), '+');
    }
}
