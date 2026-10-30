<?php

namespace App\Console\Commands;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('aksata:export-static {--out=../docs : Folder tujuan (relatif ke folder laravel)}')]
#[Description('Ekspor data & foto database ke data.json + img/ untuk situs statis (GitHub Pages / Apps Script)')]
class ExportStaticSite extends Command
{
    public function handle(): int
    {
        $out = base_path($this->option('out'));
        foreach (['img/frames', 'img/gallery'] as $dir) {
            is_dir("$out/$dir") || mkdir("$out/$dir", 0775, true);
        }

        $data = [
            'settings' => Setting::allValues(),
            'packages' => Package::active()->get()->map(fn (Package $p) => [
                'id' => (string) $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'duration_value' => $p->duration_value,
                'duration_unit' => $p->duration_unit,
                'price_note' => $p->price_note,
                'price_amount' => $p->price_amount,
                'price_style' => $p->price_style ?? 'k',
                'old_price_amount' => $p->old_price_amount,
                'addon_price_amount' => $p->addon_price_amount,
                'addon_text' => $p->addon_text,
                'tag' => $p->tag,
                'color' => $p->color_hex,
                'features' => $p->features ?? [],
                'is_featured' => (bool) $p->is_featured,
            ])->all(),
            'frames' => Frame::active()->get()->map(fn (Frame $f) => [
                'id' => (string) $f->id,
                'name' => $f->name,
                'label' => $f->label,
                'type' => $f->type,
                'size' => $f->size,
                'color' => $f->color_hex,
                'image' => $this->image($f->image_path, "$out/img/frames", 'img/frames', 1200),
            ])->all(),
            'gallery' => GalleryItem::active()->get()->map(fn (GalleryItem $g) => [
                'id' => (string) $g->id,
                'title' => $g->title,
                'category' => $g->category,
                'image' => $this->image($g->image_path, "$out/img/gallery", 'img/gallery', 1400),
            ])->all(),
            'instagram' => InstagramPost::active()->get()->map(fn (InstagramPost $i) => ['id' => (string) $i->id, 'url' => $i->url])->all(),
            'updated_at' => now()->toIso8601String(),
        ];

        file_put_contents("$out/data.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
        $this->info(sprintf('data.json: %d paket, %d frame, %d galeri, %d instagram', count($data['packages']), count($data['frames']), count($data['gallery']), count($data['instagram'])));

        return self::SUCCESS;
    }

    /** Salin gambar sebagai WebP yang diperkecil; kembalikan path relatif situs (atau URL penuh apa adanya). */
    private function image(?string $path, string $dir, string $rel, int $maxSide): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            $this->warn("Berkas tidak ada: $path");

            return null;
        }

        $name = pathinfo($path, PATHINFO_FILENAME).'.webp';
        $target = "$dir/$name";

        if (! is_file($target)) {
            try {
                $image = imagecreatefromstring($disk->get($path));
                $w = imagesx($image);
                $h = imagesy($image);
                $scale = min(1, $maxSide / max($w, $h));
                if ($scale < 1) {
                    $image = imagescale($image, (int) round($w * $scale), (int) round($h * $scale), IMG_BICUBIC);
                }
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                imagewebp($image, $target, 82);
            } catch (Throwable $e) {
                $this->warn("Gagal memproses $path: {$e->getMessage()}");

                return null;
            }
        }

        return "$rel/$name";
    }
}
