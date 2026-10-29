<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/** Menyamakan unggahan satu file (`image`) dan banyak file (`image[]`) menjadi daftar. */
class UploadName
{
    public static function normalize(Request $request): void
    {
        $file = $request->files->get('image');

        if ($file instanceof SymfonyUploadedFile) {
            $request->files->set('image', [$file]);
        }
    }
}
