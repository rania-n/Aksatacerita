<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/** Simpan gambar unggahan: perkecil, perbaiki orientasi HP, dan kompres (WebP/JPEG). Bila GD tidak ada, simpan apa adanya. */
class ImageUploader
{
    public static function store(UploadedFile $file, string $directory, int $maxSide = 1600): string
    {
        return self::compress($file, $directory, $maxSide) ?? $file->store($directory, 'public');
    }

    private static function compress(UploadedFile $file, string $directory, int $maxSide): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $level = ob_get_level();

        try {
            $source = self::open($file);
            if (! $source) {
                return null;
            }

            $source = self::orient($source, $file);
            $width = imagesx($source);
            $height = imagesy($source);
            $scale = min(1, $maxSide / max($width, $height));

            if ($scale < 1) {
                $source = imagescale($source, (int) round($width * $scale), (int) round($height * $scale), IMG_BICUBIC) ?: $source;
            }

            $webp = function_exists('imagewebp');
            $extension = $webp ? 'webp' : 'jpg';
            $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

            ob_start();
            if ($webp) {
                imagepalettetotruecolor($source);
                imagealphablending($source, true);
                imagesavealpha($source, true);
                imagewebp($source, null, 82);
            } else {
                $flat = imagecreatetruecolor(imagesx($source), imagesy($source));
                imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
                imagecopy($flat, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));
                imagejpeg($flat, null, 82);
            }
            $binary = ob_get_clean();

            if (! $binary || ! Storage::disk('public')->put($path, $binary)) {
                return null;
            }

            return $path;
        } catch (Throwable) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            return null;
        }
    }

    private static function open(UploadedFile $file): ?GdImage
    {
        $data = @file_get_contents($file->getRealPath());
        if ($data === false || ! self::ensureMemoryFor($data)) {
            return null;
        }
        $image = @imagecreatefromstring($data);

        return $image ?: null;
    }

    /** Gambar besar (mis. PNG 3750x5625) butuh ratusan MB saat didekode; naikkan batas bila boleh, kalau tidak lewati kompresi. */
    private static function ensureMemoryFor(string $data): bool
    {
        $size = @getimagesizefromstring($data);
        if (! $size) {
            return false;
        }

        $needed = (int) ($size[0] * $size[1] * 4 * 2.5) + 32 * 1024 * 1024;
        $limit = self::bytes((string) ini_get('memory_limit'));
        if ($limit === -1) {
            return true;
        }
        if ($limit - memory_get_usage() >= $needed) {
            return true;
        }

        $target = memory_get_usage() + $needed;

        return $target <= 1024 * 1024 * 1024 && @ini_set('memory_limit', (string) $target) !== false;
    }

    private static function bytes(string $value): int
    {
        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }
        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /** Foto dari HP sering tersimpan "miring" lewat tanda EXIF; putar sesuai aslinya. */
    private static function orient(GdImage $image, UploadedFile $file): GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }
}
