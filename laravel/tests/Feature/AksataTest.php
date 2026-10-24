<?php

namespace Tests\Feature;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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
        foreach (['/admin', '/admin/paket', '/admin/frame', '/admin/galeri', '/admin/instagram', '/admin/info'] as $url) {
            $this->get($url)->assertNotFound(); // tamu tidak tahu area admin ada
        }
        $this->post('/admin/paket', ['name' => 'x', 'price' => '1', 'color' => 'blue'])->assertNotFound();
    }

    public function test_non_admin_user_cannot_login_or_enter_admin(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123', 'is_admin' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($user)->get('/admin')->assertNotFound();
    }

    public function test_public_page_has_no_admin_link(): void
    {
        $this->get('/')->assertOk()->assertDontSee('/admin', false)->assertDontSee('/login', false);
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
        foreach (['/admin', '/admin/paket', '/admin/paket/1', '/admin/frame', '/admin/frame/1', '/admin/galeri', '/admin/galeri/1', '/admin/instagram', '/admin/info'] as $url) {
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

    /** @return array<string, mixed> */
    private function settingsPayload(array $override = []): array
    {
        return $override + [
            'studio_name' => 'Studio Baru', 'city' => 'Blitar', 'address' => "Jl. Baru 1\nKota",
            'latitude' => '-8.1', 'longitude' => '112.2', 'maps_url' => '',
            'open_days' => 'Senin - Sabtu', 'open_hours' => '09.00 — 21.00 WIB', 'last_session' => 'Last session 20.30', 'ticker' => 'BUKA SETIAP HARI',
            'whatsapp' => '0812-3456-789', 'wa_message' => 'Halo studio! Mau pesan ', 'email' => 'halo@studio.test',
            'instagram' => '@studio.baru', 'tiktok' => '', 'facebook' => '',
            'hero_line1' => 'Baris Satu', 'hero_line2' => 'Baris Dua', 'hero_highlight' => 'Sorot.', 'hero_text' => 'Kalimat pembuka baru.',
            'proof_show' => 1, 'proof_rating' => '5 / 5', 'proof_text' => 'pelanggan puas', 'show_frames' => 1, 'show_booth' => 1, 'show_instagram' => 1,
            'gallery_categories' => 'Event, Prewed', 'meta_title' => 'Judul Google', 'meta_description' => 'Deskripsi Google',
        ];
    }

    public function test_settings_update_shows_on_public_page(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/info', $this->settingsPayload())->assertRedirect('/admin/info');

        $this->get('/')->assertOk()
            ->assertSee('Studio Baru')->assertSee('Senin - Sabtu')->assertSee('09.00 — 21.00 WIB')
            ->assertSee('6281234567')->assertSee('studio.baru')->assertSee('Baris Satu')->assertSee('Sorot.')->assertSee('5 / 5')
            ->assertSee('-8.1%2C112.2', false)->assertSee('halo@studio.test')->assertSee('Judul Google')
            ->assertSee('Halo%20studio%21', false)->assertSee('BLITAR');
        $this->assertSame('628123456789', Setting::get('whatsapp'));
        $this->assertSame('studio.baru', Setting::get('instagram'));
    }

    public function test_settings_validation(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/info', $this->settingsPayload(['whatsapp' => 'abc']))->assertSessionHasErrors('whatsapp');
        $this->put('/admin/info', $this->settingsPayload(['maps_url' => 'javascript:alert(1)']))->assertSessionHasErrors('maps_url');
        $this->put('/admin/info', $this->settingsPayload(['facebook' => 'ftp://x']))->assertSessionHasErrors('facebook');
        $this->put('/admin/info', $this->settingsPayload(['latitude' => '95']))->assertSessionHasErrors('latitude');
        $this->put('/admin/info', $this->settingsPayload(['longitude' => null]))->assertSessionHasErrors('longitude');
        $this->put('/admin/info', $this->settingsPayload(['instagram' => 'a b!']))->assertSessionHasErrors('instagram');
        $this->assertSame('Ruma Pinara', Setting::get('studio_name'));
    }

    public function test_settings_optional_fields_can_be_cleared_and_pin_is_optional(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/info', $this->settingsPayload(['latitude' => null, 'longitude' => null, 'ticker' => '', 'last_session' => '', 'proof_show' => 0, 'show_frames' => 0, 'show_booth' => 0]))
            ->assertRedirect('/admin/info');

        $this->assertSame('', Setting::get('ticker'));
        $this->get('/')->assertOk()
            ->assertDontSee('class="ticker"', false)->assertDontSee('5 / 5')->assertDontSee('Try the booth')
            ->assertSee('q=Studio%20Baru', false); // peta jatuh ke pencarian alamat
    }

    public function test_instagram_url_and_tiktok_handles_are_normalized(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/info', $this->settingsPayload(['instagram' => 'https://www.instagram.com/nama.studio/?hl=id', 'tiktok' => '@nama_tt']))->assertRedirect('/admin/info');
        $this->assertSame('nama.studio', Setting::get('instagram'));
        $this->assertSame('nama_tt', Setting::get('tiktok'));
        $this->get('/')->assertSee('tiktok.com/@nama_tt', false);
    }

    public function test_package_extra_fields_and_single_featured(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/paket', ['name' => 'A', 'description' => 'Deskripsi A', 'duration' => '20 menit', 'price_note' => 'Hanya', 'price' => 'Rp 50.000', 'price_unit' => '/ sesi', 'old_price' => 'Rp 70.000', 'color' => 'blue', 'is_featured' => 1, 'is_active' => 1])->assertRedirect('/admin/paket');
        $this->post('/admin/paket', ['name' => 'B', 'price' => '10K', 'color' => 'pink', 'is_featured' => 1, 'is_active' => 1])->assertRedirect('/admin/paket');

        $a = Package::where('name', 'A')->firstOrFail();
        $this->assertSame('50.000', $a->price);
        $this->assertSame('70.000', $a->old_price);
        $this->assertFalse($a->is_featured); // dilepas karena B jadi unggulan
        $this->assertTrue(Package::where('name', 'B')->first()->is_featured);

        $this->get('/')->assertSee('Deskripsi A')->assertSee('20 menit')->assertSee('/ sesi')->assertSee('Rp70.000')->assertSee('Hanya');
    }

    public function test_reorder_moves_items_and_ignores_edges(): void
    {
        $this->actingAs($this->admin());
        $a = Package::create(['name' => 'A', 'price' => '1', 'color' => 'blue', 'sort_order' => 0]);
        $b = Package::create(['name' => 'B', 'price' => '1', 'color' => 'blue', 'sort_order' => 0]); // urutan sama

        $this->post("/admin/urutan/paket/{$b->id}/up")->assertRedirect('/admin/paket');
        $this->assertSame(['B', 'A'], Package::orderBy('sort_order')->orderBy('id')->pluck('name')->all());

        $this->post("/admin/urutan/paket/{$b->id}/up")->assertRedirect('/admin/paket'); // sudah paling atas
        $this->assertSame(['B', 'A'], Package::orderBy('sort_order')->orderBy('id')->pluck('name')->all());

        $this->post("/admin/urutan/paket/{$b->id}/down")->assertRedirect('/admin/paket');
        $this->assertSame(['A', 'B'], Package::orderBy('sort_order')->orderBy('id')->pluck('name')->all());

        $this->post('/admin/urutan/paket/999/up')->assertNotFound();
        $this->post("/admin/urutan/hacker/{$a->id}/up")->assertNotFound();
        $this->post("/admin/urutan/paket/{$a->id}/sideways")->assertNotFound();
    }

    public function test_gallery_categories_come_from_settings(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $this->put('/admin/info', $this->settingsPayload(['gallery_categories' => 'Event, Prewed']))->assertRedirect('/admin/info');

        $this->post('/admin/galeri', ['title' => 'x', 'category' => 'Prewed', 'image' => UploadedFile::fake()->image('a.jpg')])->assertRedirect('/admin/galeri');
        $this->post('/admin/galeri', ['title' => 'x', 'category' => 'Couple', 'image' => UploadedFile::fake()->image('a.jpg')])->assertSessionHasErrors('category');

        // kategori lama yang sudah dihapus dari daftar tetap bisa disimpan untuk foto yang memakainya
        $item = GalleryItem::create(['title' => 'Lama', 'category' => 'Wisuda', 'image_path' => 'https://example.com/a.jpg']);
        $this->put("/admin/galeri/{$item->id}", ['title' => 'Lama 2', 'category' => 'Wisuda', 'is_active' => 1])->assertRedirect('/admin/galeri');

        $this->get('/')->assertSee('data-cat="Prewed"', false)->assertSee('data-cat="Wisuda"', false);
    }

    public function test_account_update_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);
        $this->actingAs($user);

        $this->get('/admin/akun')->assertOk();
        $this->put('/admin/akun', ['name' => 'Baru', 'email' => $user->email, 'current_password' => 'salah'])->assertSessionHasErrors('current_password');
        $this->put('/admin/akun', ['name' => 'Baru', 'email' => $user->email, 'current_password' => 'rahasia123', 'password' => 'pendek', 'password_confirmation' => 'pendek'])->assertSessionHasErrors('password');
        $this->put('/admin/akun', ['name' => 'Baru', 'email' => $user->email, 'current_password' => 'rahasia123', 'password' => 'passwordbaru1', 'password_confirmation' => 'passwordbaru1'])->assertRedirect('/admin/akun');

        $user->refresh();
        $this->assertSame('Baru', $user->name);
        $this->assertTrue(Hash::check('passwordbaru1', $user->password));

        $other = User::factory()->create();
        $this->put('/admin/akun', ['name' => 'Baru', 'email' => $other->email, 'current_password' => 'passwordbaru1'])->assertSessionHasErrors('email');
    }

    public function test_default_login_notice_and_security_headers(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'admin@aksatacerita.test']));
        $this->get('/admin')->assertOk()->assertSee('Ganti email')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_public_disk_serves_from_public_uploads_without_storage_link(): void
    {
        $this->assertSame(public_path('uploads'), config('filesystems.disks.public.root'));
        $this->assertSame('/uploads/gallery/a.jpg', \App\Models\Media::url('gallery/a.jpg'));
        $this->assertSame('https://x.test/a.jpg', \App\Models\Media::url('https://x.test/a.jpg'));
    }

    public function test_apostrophes_in_content_do_not_break_pages(): void
    {
        $this->seed();
        $this->actingAs($this->admin());
        Package::create(['name' => "Mom's \"Best\" Pack", 'price' => '1K', 'color' => 'blue']);
        GalleryItem::create(['title' => "It's us", 'category' => 'Event', 'image_path' => "https://example.com/a'b.jpg"]);
        $this->get('/admin/paket')->assertOk()->assertDontSee("confirm('Hapus paket Mom's", false);
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString("a'b.jpg", $html); // tidak ada apostrof mentah di atribut
    }

    public function test_instagram_posts_managed_in_admin_and_shown_publicly(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/instagram', ['url' => 'https://instagram.com/p/AbC123/?igsh=xyz', 'is_active' => 1])->assertRedirect('/admin/instagram');
        $post = InstagramPost::firstOrFail();
        $this->assertSame('https://www.instagram.com/p/AbC123/', $post->url);

        $this->post('/admin/instagram', ['url' => 'https://www.instagram.com/p/AbC123/'])->assertSessionHasErrors('url'); // duplikat
        $this->post('/admin/instagram', ['url' => 'https://evil.com/p/AbC123/'])->assertSessionHasErrors('url');
        $this->post('/admin/instagram', ['url' => 'https://www.instagram.com/aksatacerita/'])->assertSessionHasErrors('url'); // profil, bukan postingan

        $this->get('/')->assertSee('https://www.instagram.com/p/AbC123/', false)->assertSee('embed.js', false);

        $this->put("/admin/instagram/{$post->id}", ['url' => $post->url])->assertRedirect('/admin/instagram'); // is_active tidak dicentang
        $this->get('/')->assertDontSee('embed.js', false);

        $this->delete("/admin/instagram/{$post->id}")->assertRedirect('/admin/instagram');
        $this->assertDatabaseCount('instagram_posts', 0);
    }

    public function test_user_content_is_escaped(): void
    {
        Package::create(['name' => '<script>alert(1)</script>', 'price' => '1K', 'color' => 'blue']);
        $this->get('/')->assertDontSee('<script>alert(1)</script>', false);
    }
}
