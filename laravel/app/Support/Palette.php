<?php

namespace App\Support;

use App\Models\Frame;
use App\Models\Package;

/** Warna kartu paket & frame: pilihan cepat + warna bebas (hex). */
class Palette
{
    public const DEFAULT = '#0022ff';

    /** @var array<string, string> hex => nama */
    public const PRESETS = [
        '#0022ff' => 'Biru',
        '#ff6738' => 'Oranye',
        '#ff79b8' => 'Pink',
        '#b7e433' => 'Hijau',
        '#8b5cf6' => 'Ungu',
        '#14b8a6' => 'Tosca',
        '#ef4444' => 'Merah',
        '#f5b301' => 'Kuning',
    ];

    private const LEGACY = ['blue' => '#0022ff', 'orange' => '#ff6738', 'pink' => '#ff79b8', 'green' => '#b7e433'];

    /** Nama lama (blue) atau hex apa pun -> "#rrggbb" huruf kecil; selain itu warna bawaan. */
    public static function normalize(?string $color): string
    {
        $color = strtolower(trim((string) $color));

        if (isset(self::LEGACY[$color])) {
            return self::LEGACY[$color];
        }

        return preg_match('/^#[0-9a-f]{6}$/', $color) ? $color : self::DEFAULT;
    }

    /** Warna teks yang terbaca di atas latar ini. */
    public static function textOn(string $hex): string
    {
        [$r, $g, $b] = sscanf(self::normalize($hex), '#%02x%02x%02x');

        return (($r * 299 + $g * 587 + $b * 114) / 1000) >= 150 ? '#111111' : '#ffffff';
    }

    /**
     * Pilihan swatch: preset + warna bebas yang pernah dipakai di paket/frame.
     *
     * @return list<string>
     */
    public static function swatches(): array
    {
        $used = Package::query()->pluck('color')->merge(Frame::query()->pluck('color'))
            ->map(fn ($c) => self::normalize($c))->unique()->diff(array_keys(self::PRESETS))->values()->all();

        return [...array_keys(self::PRESETS), ...$used];
    }

    public static function name(string $hex): string
    {
        return self::PRESETS[self::normalize($hex)] ?? strtoupper(self::normalize($hex));
    }
}
