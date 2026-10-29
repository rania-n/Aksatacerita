<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/** Nama/judul untuk unggahan; unggah banyak file sekaligus diberi nomor atau memakai nama berkas. */
class UploadName
{
    /** Terima satu file (`image`) maupun banyak (`image[]`); keduanya dijadikan daftar. */
    public static function normalize(Request $request): void
    {
        $file = $request->files->get('image');

        if ($file instanceof SymfonyUploadedFile) {
            $request->files->set('image', [$file]);
        }
    }

    public static function for(?string $base, UploadedFile $file, int $index, int $total, int $maxLength): string
    {
        $base = trim((string) $base);

        if ($total === 1 && $base !== '') {
            return $base;
        }

        $name = $base !== ''
            ? $base.' '.($index + 1)
            : Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return Str::limit($name, $maxLength, '');
    }
}
