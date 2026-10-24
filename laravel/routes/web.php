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

    foreach ([
        ['paket', 'packages', Admin\PackageController::class, 'package'],
        ['frame', 'frames', Admin\FrameController::class, 'frame'],
        ['galeri', 'gallery', Admin\GalleryController::class, 'item'],
        ['instagram', 'instagram', Admin\InstagramController::class, 'post'],
    ] as [$path, $name, $controller, $param]) {
        Route::get("/{$path}", [$controller, 'index'])->name("{$name}.index");
        Route::get("/{$path}/buat", [$controller, 'create'])->name("{$name}.create");
        Route::post("/{$path}", [$controller, 'store'])->name("{$name}.store");
        Route::get("/{$path}/{{$param}}/ubah", [$controller, 'edit'])->whereNumber($param)->name("{$name}.edit");
        Route::put("/{$path}/{{$param}}", [$controller, 'update'])->whereNumber($param)->name("{$name}.update");
        Route::delete("/{$path}/{{$param}}", [$controller, 'destroy'])->whereNumber($param)->name("{$name}.destroy");
    }

    Route::post('/urutan/{type}/{id}/{direction}', Admin\ReorderController::class)->whereNumber('id')->name('reorder');

    Route::get('/akun', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [Admin\AccountController::class, 'update'])->name('account.update');

    Route::get('/info', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/info', [Admin\SettingController::class, 'update'])->name('settings.update');
});
