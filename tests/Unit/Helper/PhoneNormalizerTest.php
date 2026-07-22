<?php

use App\Helper\PhoneNormalizer;

it('normalizes phone numbers to E.164 without the leading plus', function () {
    expect(PhoneNormalizer::normalize('+1 202-555-0143'))->toBe('12025550143');
    expect(PhoneNormalizer::normalize('+380 50 123 45 67'))->toBe('380501234567');
});
