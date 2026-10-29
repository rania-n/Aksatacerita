<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@aksatacerita.test')],
            ['name' => 'Admin Aksata', 'password' => env('ADMIN_PASSWORD', 'aksata2025'), 'is_admin' => true],
        );

        Setting::put([
            'options_frame_type' => 'Frame Biasa, Frame Koran, ID Card, DVD Photobooth',
            'gallery_categories' => 'Bestie, Couple, Event, Wisuda',
        ]);

        if (Package::count() === 0) {
            foreach ([
                ['Regular Photobox', 35000, 'Most Picked', '#0022ff', ['2 strip cetak', 'All soft files', 'Pilihan frame bebas'], 15, true],
                ['Self Photo Studio', 75000, 'Slow & Sweet', '#ff6738', ['2 foto cetak 4R', 'All soft files', 'Wireless shutter remote', 'Full studio lighting'], 30, false],
                ['Passport Booth', 25000, 'Ready in 10 min', '#ff79b8', ['6 pas foto', 'Background pilihan', 'File high-res'], 10, false],
            ] as $i => [$name, $price_amount, $tag, $color, $features, $duration_value, $is_featured]) {
                Package::create(compact('name', 'price_amount', 'tag', 'color', 'features', 'duration_value', 'is_featured') + ['price_note' => 'Mulai dari', 'sort_order' => $i]);
            }
        }

        if (Frame::count() === 0) {
            $framesDir = public_path('uploads/frames');
            if (is_dir($framesDir)) {
                $files = glob($framesDir . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: [];
                $labels = ['SIGNATURE', 'NEW DROP', 'LIMITED', 'FAVORITE', 'BEST SELLER'];
                $colors = ['#0022ff', '#ff6738', '#b7e433', '#ff79b8', '#111111', '#5d44f8', '#ff385c'];
                foreach ($files as $i => $file) {
                    $fn = basename($file);
                    $type = 'Frame Biasa';
                    if (str_contains(strtoupper($fn), 'KORAN')) {
                        $type = 'Frame Koran';
                    } elseif (str_contains(strtoupper($fn), 'ID_CARD') || preg_match('/^\d+\.png$/', $fn)) {
                        $type = 'ID Card';
                    } elseif (str_contains($fn, '20260805') || str_contains($fn, 'IMG_03')) {
                        $type = 'DVD Photobooth';
                    }

                    $clean = preg_replace('/[_\d]+$/', '', pathinfo($fn, PATHINFO_FILENAME));
                    $clean = trim(str_replace(['_', '-'], ' ', $clean));
                    $name = strlen($clean) < 3 ? "Aksata {$type} " . str_pad($i + 1, 2, '0', STR_PAD_LEFT) : ucwords($clean) . ' ' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);

                    Frame::create([
                        'name' => $name,
                        'label' => $labels[$i % count($labels)],
                        'type' => $type,
                        'size' => ($i % 2 === 0) ? '2R' : '4R',
                        'color' => $colors[$i % count($colors)],
                        'image_path' => 'frames/' . $fn,
                        'sort_order' => $i,
                        'is_active' => true,
                    ]);
                }
            }
        }

        if (GalleryItem::count() === 0) {
            $galleryDir = public_path('uploads/gallery');
            if (is_dir($galleryDir)) {
                $files = glob($galleryDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
                $categories = ['Bestie', 'Couple', 'Event', 'Wisuda'];
                $titles = [
                    'Sunday with Besties', 'Us Being Us', 'Birthday Chaos', 'Finally Graduated!',
                    'Main Character Energy', 'A Little Celebration', 'Happy Moments in Studio', 'Say Cheese!',
                    'Squad Goals', 'Sweet Date in Frame', 'Our Kind of Fun', 'Memories to Keep',
                ];
                $order = 0;
                foreach ($files as $file) {
                    $fn = basename($file);
                    if (str_starts_with($fn, 'AKSATA')) {
                        continue;
                    }
                    GalleryItem::create([
                        'title' => $titles[$order % count($titles)],
                        'category' => $categories[$order % count($categories)],
                        'image_path' => 'gallery/' . $fn,
                        'sort_order' => $order++,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
