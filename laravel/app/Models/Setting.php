<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** Nilai bawaan; juga daftar semua kunci pengaturan yang dikenal. */
    public const DEFAULTS = [
        // Studio & lokasi
        'studio_name' => 'Ruma Pinara',
        'city' => 'Tulungagung',
        'address' => "Jl. I Gusti Ngurah Rai Gg. 8 No. 90,\nJepun, Tulungagung, Jawa Timur",
        'latitude' => '-8.0654',
        'longitude' => '111.9025',
        'maps_url' => '',
        'open_days' => 'Setiap Hari',
        'open_hours' => '10.00 — 22.00 WIB',
        'last_session' => 'Last session 21.30',
        'ticker' => 'STUDIO OPEN DAILY · 10.00—22.00 WIB',
        // Kontak & sosial media
        'whatsapp' => '6285117375305',
        'wa_message' => 'Halo Aksatacerita! Aku mau pesan ',
        'email' => '',
        'instagram' => 'aksatacerita',
        'tiktok' => '',
        'facebook' => '',
        // Teks halaman depan
        'hero_line1' => 'Abadikan cerita',
        'hero_line2' => 'serumu di',
        'hero_highlight' => 'Aksata.',
        'hero_text' => 'Masuk, pilih pose paling random, lalu bawa pulang potongan cerita yang nggak akan terulang dua kali.',
        'proof_show' => '1',
        'proof_rating' => '4.9 / 5',
        'proof_text' => 'disayang 2,000+ teman cerita',
        // Bagian yang ditampilkan
        'show_frames' => '1',
        'show_booth' => '1',
        'show_instagram' => '1',
        'gallery_categories' => 'Event, Wisuda, Couple, Bestie',
        // Google
        'meta_title' => 'Aksata Cerita — Photobooth Studio Tulungagung',
        'meta_description' => 'Aksata Cerita, photobooth studio di Tulungagung. Pilih paket, pilih frame, abadikan ceritamu.',
    ];

    /** @var array<string, string>|null */
    private static ?array $cache = null;

    public static function get(string $key): ?string
    {
        return static::allValues()[$key] ?? null;
    }

    public static function bool(string $key): bool
    {
        return static::get($key) === '1';
    }

    /**
     * Nilai yang dipisah koma/baris baru menjadi daftar.
     *
     * @return list<string>
     */
    public static function list(string $key): array
    {
        return static::parseList((string) static::get($key));
    }

    /** @return list<string> */
    public static function parseList(string $value): array
    {
        return array_values(array_unique(array_filter(array_map('trim', preg_split('/[,\n]+/', $value) ?: []))));
    }

    /**
     * Semua setting (dengan default). Nilai kosong yang disimpan admin tetap kosong, tidak kembali ke default.
     *
     * @return array<string, string>
     */
    public static function allValues(): array
    {
        return static::$cache ??= array_merge(
            static::DEFAULTS,
            array_map(fn ($v) => (string) $v, static::all()->pluck('value', 'key')->all()),
        );
    }

    /** @param array<string, string|null> $values */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }
        static::flush();
    }

    public static function flush(): void
    {
        static::$cache = null;
    }
}
