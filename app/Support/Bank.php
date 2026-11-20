<?php

namespace App\Support;

class Bank
{
    public const BANKS = [
        'BCA' => 'Bank Central Asia (BCA)',
        'BRI' => 'Bank Rakyat Indonesia (BRI)',
        'Mandiri' => 'Bank Mandiri',
        'BNI' => 'Bank Negara Indonesia (BNI)',
        'BSI' => 'Bank Syariah Indonesia (BSI)',
        'CIMB' => 'CIMB Niaga',
        'Permata' => 'Bank Permata',
        'Danamon' => 'Bank Danamon',
        'BTN' => 'Bank BTN',
        'Panin' => 'Bank Panin',
        'OCBC' => 'OCBC NISP',
        'Maybank' => 'Maybank Indonesia',
        'Mega' => 'Bank Mega',
        'Sinarmas' => 'Bank Sinarmas',
        'BTPN' => 'Bank BTPN / Jenius',
        'Jago' => 'Bank Jago',
        'SeaBank' => 'SeaBank',
        'Blu' => 'blu by BCA Digital',
        'LineBank' => 'Line Bank by Hana Bank',
        'Neo' => 'Bank Neo Commerce',
    ];

    public const EWALLETS = [
        'DANA' => 'DANA',
        'GoPay' => 'GoPay',
        'OVO' => 'OVO',
        'ShopeePay' => 'ShopeePay',
        'LinkAja' => 'LinkAja',
        'i.saku' => 'i.saku',
    ];

    public static function all(): array
    {
        return array_merge(self::BANKS, self::EWALLETS, ['Lainnya' => 'Bank / Dompet Digital Lainnya']);
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function label(?string $key): string
    {
        if (!$key) {
            return '-';
        }
        $all = self::all();
        return $all[$key] ?? $key;
    }
}
