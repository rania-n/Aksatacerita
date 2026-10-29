<?php

namespace Tests\Feature;

use App\Models\Frame;
use Database\Seeders\FrameAssetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FrameAssetSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_every_asset_file_as_a_frame_with_its_image_stored(): void
    {
        Storage::fake('public');
        $assetCount = count(glob(database_path('seeders/assets/*/*.{png,jpg,jpeg,webp}'), GLOB_BRACE));

        $this->seed(FrameAssetSeeder::class);

        $this->assertSame($assetCount, Frame::count());
        Frame::all()->each(fn (Frame $frame) => Storage::disk('public')->assertExists($frame->image_path));
        $this->assertEqualsCanonicalizing(
            ['Frame Biasa', 'Frame Koran', 'ID Card', 'DVD Photobooth'],
            Frame::pluck('type')->unique()->values()->all(),
        );
    }

    public function test_seeding_twice_does_not_duplicate_or_overwrite_admin_edits_and_restores_missing_files(): void
    {
        Storage::fake('public');
        $this->seed(FrameAssetSeeder::class);
        $total = Frame::count();
        $frame = Frame::first();
        $frame->update(['name' => 'Diubah admin']);
        Storage::disk('public')->delete($frame->image_path);

        $this->seed(FrameAssetSeeder::class);

        $this->assertSame($total, Frame::count());
        $this->assertSame('Diubah admin', $frame->fresh()->name);
        Storage::disk('public')->assertExists($frame->image_path);
    }
}
