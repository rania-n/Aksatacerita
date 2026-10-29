<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\Setting;
use App\Support\SeedAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Mengisi tabel frames dari berkas di database/seeders/assets (ikut ter-commit),
 * sehingga semua orang mendapat foto frame yang sama setelah `php artisan migrate --seed`.
 * Aman dijalankan berulang: frame yang sudah ada tidak diubah, berkas yang hilang disalin ulang.
 */
class FrameAssetSeeder extends Seeder
{
    /**
     * Jenis frame => awalan nama.
     *
     * @var array<string, string>
     */
    private const PREFIXES = [
        'Frame Biasa' => 'Frame',
        'Frame Koran' => 'Koran',
        'ID Card' => 'ID Card',
        'DVD Photobooth' => 'DVD',
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');
        $sortOrder = 0;

        foreach (SeedAssets::FRAME_FOLDERS as $type => $folder) {
            $prefix = self::PREFIXES[$type];
            $files = glob(database_path("seeders/assets/{$folder}/*.{png,jpg,jpeg,webp}"), GLOB_BRACE) ?: [];
            natcasesort($files);

            foreach (array_values($files) as $index => $file) {
                $imagePath = SeedAssets::storedPath('frames', $file);

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
