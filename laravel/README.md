# Aksata Cerita (Laravel Blade)

Website publik + admin untuk Aksata Cerita. Data (paket, frame, galeri, info studio) disimpan di SQLite.

## Menjalankan
```bash
cd laravel
composer install            # hanya jika vendor/ belum ada
cp .env.example .env && php artisan key:generate   # hanya jika .env belum ada
php artisan migrate --seed
php artisan serve
```
- Website: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin (otomatis diarahkan ke /login)

## Login admin awal
- Email: `admin@aksatacerita.test`
- Password: `aksata2025`

Ganti sebelum online. Set `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`, lalu jalankan `php artisan db:seed`.
Atau pakai tinker:
`php artisan tinker --execute="App\Models\User::first()->update(['password'=>'PASSWORD_BARU'])"`

## Akses admin
- Tidak ada link admin di website publik. Admin masuk langsung lewat `/login`.
- Hanya user dengan `is_admin = true` yang bisa login; tamu yang membuka `/admin` mendapat 404.
- Tambah admin: `php artisan tinker --execute="App\Models\User::create(['name'=>'Nama','email'=>'a@b.com','password'=>'...','is_admin'=>true])"`

## Fitur admin
(Foto unggahan disimpan di `public/uploads/`, jadi tidak perlu `storage:link`.)

Paket & Harga (durasi, harga coret, satuan, paket unggulan, urutan), Katalog Frame, Galeri Momen (kategori bisa diatur),
Instagram (tempel link postingan), Info Studio (lokasi dengan pin peta, jam buka, kontak & sosial media, teks halaman depan, tampilan Google), Akun (ganti email/password).

## Tes
`php artisan test`

## Saat deploy
Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` ke domain asli. Arahkan document root ke `public/`.
Folder `storage/` dan `database/` harus bisa ditulis oleh web server.
