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

    /**
     * Salin berkas di disk public ke folder aset dan catat metadatanya (nama, ukuran, label, judul) di manifest.json.
     * Salinan lama di folder lain (mis. jenis berubah) dihapus.
     *
     * @param  array<string, string|null>  $meta
     */
    public static function sync(?string $storedPath, string $folder, array $meta = []): void
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

        $manifest = self::manifest();
        $manifest["{$folder}/".basename($storedPath)] = array_filter($meta, fn ($value) => $value !== null && $value !== '');
        self::saveManifest($manifest);
    }

    /**
     * Metadata berkas aset dari manifest.json, kunci "FOLDER/berkas".
     *
     * @return array<string, string>
     */
    public static function meta(string $key): array
    {
        return self::manifest()[$key] ?? [];
    }

    /** Hapus baris model yang gambar lokalnya tidak ada lagi di disk (mis. file upload orang lain yang tidak ikut git). */
    public static function pruneMissing(string $modelClass): void
    {
        $modelClass::query()->get()
            ->filter(fn ($row) => $row->image_path && ! str_starts_with($row->image_path, 'http') && ! Storage::disk('public')->exists($row->image_path))
            ->each->delete();
    }

    public static function forget(?string $storedPath): void
    {
        if (! self::enabled($storedPath)) {
            return;
        }

        foreach (glob(database_path('seeders/assets/{*,GALERI/*}/'.basename($storedPath)), GLOB_BRACE) ?: [] as $copy) {
            unlink($copy);
        }

        $manifest = self::manifest();
        $remaining = array_filter($manifest, fn (string $key) => basename($key) !== basename($storedPath), ARRAY_FILTER_USE_KEY);

        if (count($remaining) !== count($manifest)) {
            self::saveManifest($remaining);
        }
    }

    /** @return array<string, array<string, string>> */
    private static function manifest(): array
    {
        $file = database_path('seeders/assets/manifest.json');

        return is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    }

    /** @param  array<string, array<string, string>>  $manifest */
    private static function saveManifest(array $manifest): void
    {
        ksort($manifest);
        $file = database_path('seeders/assets/manifest.json');
        is_dir(dirname($file)) || mkdir(dirname($file), 0775, true);
        file_put_contents(
            $file,
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n",
        );
    }

    private static function enabled(?string $storedPath): bool
    {
        return $storedPath && ! str_starts_with($storedPath, 'http') && app()->environment('local');
    }
}
