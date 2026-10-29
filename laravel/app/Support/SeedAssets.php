<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Menyalin gambar unggahan admin ke database/seeders/assets agar ikut ter-commit
 * dan muncul di DB semua orang lewat seeder. Hanya aktif di environment local.
 */
class SeedAssets
{
    /** Jenis frame => folder aset. */
    public const FRAME_FOLDERS = [
        'Frame Biasa' => 'FRAME BIASA',
        'Frame Koran' => 'FRAME KORAN',
        'ID Card' => 'ID CARD PHOTOBOOTH',
        'DVD Photobooth' => 'DVD PHOTOBOOTH',
    ];

    public const GALLERY_FOLDER = 'GALERI';

    public static function frameFolder(?string $type): string
    {
        return self::FRAME_FOLDERS[$type] ?? self::FRAME_FOLDERS['Frame Biasa'];
    }

    public static function galleryFolder(string $category): string
    {
        return self::GALLERY_FOLDER.'/'.(trim(str_replace(['/', '\\', '.'], ' ', $category)) ?: 'Umum');
    }

    /** Nama berkas di disk public untuk berkas aset; nama yang sudah aman dipakai apa adanya. */
    public static function storedPath(string $directory, string $assetFile): string
    {
        $name = basename($assetFile);

        if (! preg_match('/^[A-Za-z0-9._-]+$/', $name)) {
            $name = Str::slug(pathinfo($name, PATHINFO_FILENAME)).'.'.strtolower(pathinfo($name, PATHINFO_EXTENSION));
        }

        return trim($directory, '/').'/'.$name;
    }

    /** Salin berkas di disk public ke folder aset; salinan lama di folder lain (mis. jenis berubah) dihapus. */
    public static function sync(?string $storedPath, string $folder): void
    {
        if (! self::enabled($storedPath) || ! Storage::disk('public')->exists($storedPath)) {
            return;
        }

        self::forget($storedPath);

        $target = database_path("seeders/assets/{$folder}");
        if (! is_dir($target)) {
            mkdir($target, 0775, true);
        }

        copy(Storage::disk('public')->path($storedPath), $target.'/'.basename($storedPath));
    }

    public static function forget(?string $storedPath): void
    {
        if (! self::enabled($storedPath)) {
            return;
        }

        foreach (glob(database_path('seeders/assets/{*,GALERI/*}/'.basename($storedPath)), GLOB_BRACE) ?: [] as $copy) {
            unlink($copy);
        }
    }

    private static function enabled(?string $storedPath): bool
    {
        return $storedPath && ! str_starts_with($storedPath, 'http') && app()->environment('local');
    }
}
