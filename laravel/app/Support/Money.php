<?php

namespace App\Support;

class Money
{
    /** 35000 -> "35K" (style k, bila pas ribuan) atau "35.000" (style rp). Tanpa awalan "Rp". */
    public static function format(?int $amount, string $style = 'k'): string
    {
        $amount ??= 0;

        if ($style === 'k' && $amount >= 1000 && $amount % 1000 === 0) {
            return ($amount / 1000).'K';
        }

        return number_format($amount, 0, ',', '.');
    }
}
