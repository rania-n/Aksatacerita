<?php

namespace Database\Seeders;

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
                ['Regular Photobox', 35000, 'Most Picked', '#0022ff', ['2 strip cetak', 'All soft files'], 15, true],
                ['Self Photo Studio', 75000, 'Slow & Sweet', '#ff6738', ['2 foto cetak 4R', 'All soft files'], 30, false],
                ['Passport Booth', 25000, 'Ready in 10 min', '#ff79b8', ['6 pas foto', 'Background pilihan', 'File high-res'], 10, false],
            ] as $i => [$name, $price_amount, $tag, $color, $features, $duration_value, $is_featured]) {
                Package::create(compact('name', 'price_amount', 'tag', 'color', 'features', 'duration_value', 'is_featured') + ['price_note' => 'Mulai dari', 'sort_order' => $i]);
            }
        }

        $this->call(FrameAssetSeeder::class);

        $this->call(GalleryAssetSeeder::class);
    }
}
