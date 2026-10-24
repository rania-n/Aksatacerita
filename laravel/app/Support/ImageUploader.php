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
        $image = $data === false ? false : @imagecreatefromstring($data);

        return $image ?: null;
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
