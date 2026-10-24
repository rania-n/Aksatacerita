<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/paket/{package?}', [Admin\PackageController::class, 'index'])->whereNumber('package')->name('packages.index');
    Route::post('/paket', [Admin\PackageController::class, 'store'])->name('packages.store');
    Route::put('/paket/{package}', [Admin\PackageController::class, 'update'])->name('packages.update');
    Route::delete('/paket/{package}', [Admin\PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/frame/{frame?}', [Admin\FrameController::class, 'index'])->whereNumber('frame')->name('frames.index');
    Route::post('/frame', [Admin\FrameController::class, 'store'])->name('frames.store');
    Route::put('/frame/{frame}', [Admin\FrameController::class, 'update'])->name('frames.update');
    Route::delete('/frame/{frame}', [Admin\FrameController::class, 'destroy'])->name('frames.destroy');

    Route::get('/galeri/{item?}', [Admin\GalleryController::class, 'index'])->whereNumber('item')->name('gallery.index');
    Route::post('/galeri', [Admin\GalleryController::class, 'store'])->name('gallery.store');
    Route::put('/galeri/{item}', [Admin\GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/galeri/{item}', [Admin\GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('/instagram/{post?}', [Admin\InstagramController::class, 'index'])->whereNumber('post')->name('instagram.index');
    Route::post('/instagram', [Admin\InstagramController::class, 'store'])->name('instagram.store');
    Route::put('/instagram/{post}', [Admin\InstagramController::class, 'update'])->name('instagram.update');
    Route::delete('/instagram/{post}', [Admin\InstagramController::class, 'destroy'])->name('instagram.destroy');

    Route::post('/urutan/{type}/{id}/{direction}', Admin\ReorderController::class)->whereNumber('id')->name('reorder');

    Route::get('/akun', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [Admin\AccountController::class, 'update'])->name('account.update');

    Route::get('/info', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/info', [Admin\SettingController::class, 'update'])->name('settings.update');
});
