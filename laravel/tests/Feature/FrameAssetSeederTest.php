<?php

namespace Tests\Feature;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\User;
use Database\Seeders\FrameAssetSeeder;
use Database\Seeders\GalleryAssetSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_gallery_seeder_uses_subfolder_as_category_and_is_idempotent(): void
    {
        Storage::fake('public');
        $assetCount = count(glob(database_path('seeders/assets/GALERI/*/*.{png,jpg,jpeg,webp}'), GLOB_BRACE));

        $this->seed(GalleryAssetSeeder::class);
        $this->seed(GalleryAssetSeeder::class);

        $this->assertGreaterThan(0, $assetCount);
        $this->assertSame($assetCount, GalleryItem::count());
        $this->assertContains('Talent', GalleryItem::pluck('category')->all());
        GalleryItem::all()->each(fn (GalleryItem $item) => Storage::disk('public')->assertExists($item->image_path));
    }

    public function test_admin_uploads_are_copied_to_seed_assets_and_removed_on_delete_when_local(): void
    {
        Storage::fake('public');
        $assets = sys_get_temp_dir().'/seed-assets-'.uniqid();
        mkdir($assets);
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->app->useDatabasePath($assets);
        $this->app['env'] = 'local';
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.frames.store'), [
            'name' => 'Frame Uji',
            'type' => 'Frame Koran',
            'image' => UploadedFile::fake()->image('a.png', 100, 100),
        ])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Foto Uji',
            'category' => 'Event',
            'image' => UploadedFile::fake()->image('b.jpg', 100, 100),
        ])->assertRedirect();

        $frame = Frame::firstOrFail();
        $item = GalleryItem::firstOrFail();
        $this->assertFileExists($assets.'/seeders/assets/FRAME KORAN/'.basename($frame->image_path));
        $this->assertFileExists($assets.'/seeders/assets/GALERI/Event/'.basename($item->image_path));

        $this->actingAs($admin)->delete(route('admin.frames.destroy', $frame))->assertRedirect();

        $this->assertFileDoesNotExist($assets.'/seeders/assets/FRAME KORAN/'.basename($frame->image_path));
    }
}
