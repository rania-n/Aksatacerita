<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\Package;
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

        if (Package::count() === 0) {
            foreach ([
                ['Regular Photobox', '35K', 'Most Picked', 'blue', ['2 strip cetak', 'All soft files'], '15 menit', true],
                ['Self Photo Studio', '75K', 'Slow & Sweet', 'orange', ['2 foto cetak 4R', 'All soft files'], '30 menit', false],
                ['Passport Booth', '25K', 'Ready in 10 min', 'pink', ['6 pas foto', 'Background pilihan', 'File high-res'], '10 menit', false],
            ] as $i => [$name, $price, $tag, $color, $features, $duration, $is_featured]) {
                Package::create(compact('name', 'price', 'tag', 'color', 'features', 'duration', 'is_featured') + ['price_note' => 'Mulai dari', 'sort_order' => $i]);
            }
        }

        if (Frame::count() === 0) {
            foreach ([
                ['Retro Blue', 'SIGNATURE', 'blue'],
                ['Tangerine Day', 'NEW DROP', 'orange'],
                ['Garden Club', 'LIMITED', 'green'],
                ['Pink Crush', 'FAVORITE', 'pink'],
            ] as $i => [$name, $label, $color]) {
                Frame::create(compact('name', 'label', 'color') + ['sort_order' => $i]);
            }
        }

        if (GalleryItem::count() === 0) {
            $u = fn (string $id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=900&q=85";
            foreach ([
                ['Sunday with besties', 'Bestie', '1631832255415-8583788a11cb'],
                ['Us being us', 'Couple', '1609084415979-e38c11583e8b'],
                ['Birthday chaos', 'Event', '1738156684532-b79bfb589344'],
                ['Finally graduated!', 'Wisuda', '1582510871461-27ff4aafa053'],
                ['Main character energy', 'Bestie', '1631821657340-b07983bfc5f9'],
                ['A little celebration', 'Event', '1631832187986-85ce443a65d9'],
            ] as $i => [$title, $category, $id]) {
                GalleryItem::create(['title' => $title, 'category' => $category, 'image_path' => $u($id), 'sort_order' => $i]);
            }
        }
    }
}
