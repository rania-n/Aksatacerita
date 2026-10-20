# Aksata Cerita (Laravel Blade)

Website publik + admin untuk Aksata Cerita. Data (paket, frame, galeri, info studio) disimpan di SQLite.

## Menjalankan
```bash
cd laravel
composer install            # hanya jika vendor/ belum ada
cp .env.example .env && php artisan key:generate   # hanya jika .env belum ada
php artisan migrate --seed
php artisan storage:link    # sekali saja, supaya foto unggahan tampil
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

## Fitur admin
Paket & Harga (tambah, ubah, hapus, sembunyikan), Katalog Frame (dengan gambar), Galeri Momen (unggah foto),
Info Studio (alamat, jam buka, WhatsApp, Instagram, Google Maps).

## Tes
`php artisan test`

## Saat deploy
Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` ke domain asli. Arahkan document root ke `public/`.
Folder `storage/` dan `database/` harus bisa ditulis oleh web server.
