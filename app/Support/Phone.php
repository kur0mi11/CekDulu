<?php

namespace App\Support;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class Phone
{
    private const REGION = 'ID';

    private const ALLOWED_TYPES = [PhoneNumberType::MOBILE];

    public static function normalize(?string $raw): ?string
    {
        $raw = trim((string) $raw);

        if ($raw === '' || mb_strlen($raw) > 30) {
            return null;
        }

        if (preg_match('/[^\d\s+\-().]/', $raw)) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $number = $util->parse($raw, self::REGION);
        } catch (NumberParseException $e) {
            return null;
        }

        if (!$util->isValidNumber($number)) {
            return null;
        }

        if ($util->getRegionCodeForNumber($number) !== self::REGION) {
            return null;
        }

        if (!in_array($util->getNumberType($number), self::ALLOWED_TYPES, true)) {
            return null;
        }

        return '0' . $number->getNationalNumber();
    }

    public static function isValid(?string $raw): bool
    {
        return self::normalize($raw) !== null;
    }
}