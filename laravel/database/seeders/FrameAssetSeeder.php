<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Mengisi tabel frames dari berkas di database/seeders/assets (ikut ter-commit),
 * sehingga semua orang mendapat foto frame yang sama setelah `php artisan migrate --seed`.
 * Aman dijalankan berulang: frame yang sudah ada tidak diubah, berkas yang hilang disalin ulang.
 */
class FrameAssetSeeder extends Seeder
{
    /**
     * Folder aset => [jenis frame, awalan nama].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const FOLDERS = [
        'FRAME BIASA' => ['Frame Biasa', 'Frame'],
        'FRAME KORAN' => ['Frame Koran', 'Koran'],
        'ID CARD PHOTOBOOTH' => ['ID Card', 'ID Card'],
        'DVD PHOTOBOOTH' => ['DVD Photobooth', 'DVD'],
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');
        $sortOrder = 0;

        foreach (self::FOLDERS as $folder => [$type, $prefix]) {
            $files = glob(database_path("seeders/assets/{$folder}/*.{png,jpg,jpeg,webp}"), GLOB_BRACE) ?: [];
            natcasesort($files);

            foreach (array_values($files) as $index => $file) {
                $imagePath = 'frames/'.Str::slug(pathinfo($file, PATHINFO_FILENAME)).'.'.strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (! $disk->exists($imagePath)) {
                    $disk->put($imagePath, file_get_contents($file));
                }

                Frame::firstOrCreate(['image_path' => $imagePath], [
                    'name' => sprintf('%s %02d', $prefix, $index + 1),
                    'type' => $type,
                    'size' => $this->sizeFor($type, basename($file)),
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]);

                $sortOrder++;
            }

            Setting::addToList('options_frame_type', $type);
        }
    }

    private function sizeFor(string $type, string $fileName): ?string
    {
        if ($type === 'DVD Photobooth') {
            return null;
        }

        return str_contains(strtoupper($fileName), '2R') ? '2R' : '4R';
    }
}
