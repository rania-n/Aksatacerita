<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\Setting;
use App\Support\SeedAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Mengisi galeri dari database/seeders/assets/GALERI/<Kategori>/*. Nama subfolder menjadi kategori.
 * Aman dijalankan berulang: item yang sudah ada tidak diubah, berkas yang hilang disalin ulang.
 */
class GalleryAssetSeeder extends Seeder
{
    public function run(): void
    {
        $disk = Storage::disk('public');
        $sortOrder = 0;

        foreach (glob(database_path('seeders/assets/'.SeedAssets::GALLERY_FOLDER.'/*'), GLOB_ONLYDIR) ?: [] as $categoryDir) {
            $category = basename($categoryDir);
            $files = glob("{$categoryDir}/*.{png,jpg,jpeg,webp}", GLOB_BRACE) ?: [];
            natcasesort($files);

            foreach (array_values($files) as $index => $file) {
                $imagePath = SeedAssets::storedPath('gallery', $file);

                if (! $disk->exists($imagePath)) {
                    $disk->put($imagePath, file_get_contents($file));
                }

                $meta = SeedAssets::meta(SeedAssets::GALLERY_FOLDER."/{$category}/".basename($file));

                GalleryItem::firstOrCreate(['image_path' => $imagePath], [
                    'title' => $meta['title'] ?? sprintf('%s %02d', $category, $index + 1),
                    'category' => $category,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]);

                $sortOrder++;
            }

            Setting::addToList('gallery_categories', $category);
        }

        SeedAssets::pruneMissing(GalleryItem::class);
    }
}
