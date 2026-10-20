<?php

namespace Tests\Feature;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AksataTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_public_page_shows_database_content(): void
    {
        Package::create(['name' => 'Paket Uji', 'price' => '99K', 'tag' => 'Baru', 'color' => 'blue', 'features' => ['Satu', 'Dua']]);
        Package::create(['name' => 'Paket Tersembunyi', 'price' => '1K', 'color' => 'pink', 'is_active' => false]);
        Frame::create(['name' => 'Frame Uji', 'label' => 'NEW', 'color' => 'green']);
        GalleryItem::create(['title' => 'Foto Uji', 'category' => 'Event', 'image_path' => 'https://example.com/a.jpg']);

        $this->get('/')->assertOk()
            ->assertSee('Paket Uji')->assertSee('99K')->assertSee('Dua')
            ->assertDontSee('Paket Tersembunyi')
            ->assertSee('Frame Uji')->assertSee('Foto Uji')->assertSee('6285117375305');
    }

    public function test_public_page_works_with_empty_database(): void
    {
        $this->get('/')->assertOk()->assertSee('Paket segera hadir');
    }

    public function test_admin_requires_login(): void
    {
        foreach (['/admin', '/admin/paket', '/admin/frame', '/admin/galeri', '/admin/info'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/admin/paket', ['name' => 'x', 'price' => '1', 'color' => 'blue'])->assertRedirect('/login');
    }

    public function test_login_logout(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])->assertRedirect('/admin');
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_all_admin_pages_render(): void
    {
        $this->seed();
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/paket', '/admin/paket/1', '/admin/frame', '/admin/frame/1', '/admin/galeri', '/admin/galeri/1', '/admin/info'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_package_crud(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/paket', ['name' => 'Bestie', 'price' => 'Rp 50K', 'color' => 'orange', 'features' => "15 menit\nSoft files, Cetak 4R", 'is_active' => 1])
            ->assertRedirect('/admin/paket');
        $p = Package::firstOrFail();
        $this->assertSame('50K', $p->price);
        $this->assertSame(['15 menit', 'Soft files', 'Cetak 4R'], $p->features);

        $this->put("/admin/paket/{$p->id}", ['name' => 'Bestie Plus', 'price' => '60K', 'color' => 'pink', 'features' => 'Satu'])->assertRedirect('/admin/paket');
        $p->refresh();
        $this->assertSame('Bestie Plus', $p->name);
        $this->assertFalse($p->is_active); // checkbox tidak dicentang = disembunyikan

        $this->get('/')->assertDontSee('Bestie Plus');

        $this->post('/admin/paket', ['name' => '', 'price' => '', 'color' => 'merah'])->assertSessionHasErrors(['name', 'price', 'color']);

        $this->delete("/admin/paket/{$p->id}")->assertRedirect('/admin/paket');
        $this->assertDatabaseCount('packages', 0);
    }

    public function test_gallery_upload_replace_delete(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->post('/admin/galeri', ['title' => 'Foto A', 'category' => 'Event', 'image' => UploadedFile::fake()->image('a.jpg')])->assertRedirect('/admin/galeri');
        $item = GalleryItem::firstOrFail();
        Storage::disk('public')->assertExists($item->image_path);

        $old = $item->image_path;
        $this->put("/admin/galeri/{$item->id}", ['title' => 'Foto B', 'category' => 'Couple', 'image' => UploadedFile::fake()->image('b.png'), 'is_active' => 1])->assertRedirect('/admin/galeri');
        $item->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($item->image_path);

        $this->post('/admin/galeri', ['title' => 'x', 'category' => 'Event'])->assertSessionHasErrors('image');
        $this->post('/admin/galeri', ['title' => 'x', 'category' => 'Event', 'image' => UploadedFile::fake()->create('virus.php', 10)])->assertSessionHasErrors('image');
        $this->post('/admin/galeri', ['title' => 'x', 'category' => 'Hacker', 'image' => UploadedFile::fake()->image('c.jpg')])->assertSessionHasErrors('category');

        $this->delete("/admin/galeri/{$item->id}")->assertRedirect('/admin/galeri');
        Storage::disk('public')->assertMissing($item->image_path);
    }

    public function test_frame_crud_with_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->post('/admin/frame', ['name' => 'Retro', 'label' => 'new', 'color' => 'green', 'image' => UploadedFile::fake()->image('f.png'), 'is_active' => 1])->assertRedirect('/admin/frame');
        $f = Frame::firstOrFail();
        $this->assertSame('NEW', $f->label);
        Storage::disk('public')->assertExists($f->image_path);

        $this->put("/admin/frame/{$f->id}", ['name' => 'Retro 2', 'color' => 'blue', 'is_active' => 1])->assertRedirect('/admin/frame');
        $this->assertSame('Retro 2', $f->fresh()->name);
        Storage::disk('public')->assertExists($f->image_path); // gambar lama tetap kalau tidak diganti

        $this->delete("/admin/frame/{$f->id}");
        Storage::disk('public')->assertMissing($f->image_path);
    }

    public function test_settings_update_shows_on_public_page(): void
    {
        $this->actingAs($this->admin());
        $data = [
            'studio_name' => 'Studio Baru', 'address' => "Jl. Baru 1\nKota", 'open_hours' => '09.00 — 21.00 WIB', 'last_session' => 'Last session 20.30',
            'whatsapp' => '628123456789', 'instagram' => 'studio.baru', 'maps_url' => 'https://maps.google.com/?q=x', 'ticker' => 'BUKA SETIAP HARI',
        ];
        $this->put('/admin/info', $data)->assertRedirect('/admin/info');
        $this->get('/')->assertSee('Studio Baru')->assertSee('09.00 — 21.00 WIB')->assertSee('628123456789')->assertSee('studio.baru');

        $this->put('/admin/info', ['whatsapp' => '08123abc'] + $data)->assertSessionHasErrors('whatsapp');
        $this->put('/admin/info', ['maps_url' => 'javascript:alert(1)'] + $data)->assertSessionHasErrors('maps_url');
        $this->assertSame('Studio Baru', Setting::get('studio_name'));
    }

    public function test_user_content_is_escaped(): void
    {
        Package::create(['name' => '<script>alert(1)</script>', 'price' => '1K', 'color' => 'blue']);
        $this->get('/')->assertDontSee('<script>alert(1)</script>', false);
    }
}
