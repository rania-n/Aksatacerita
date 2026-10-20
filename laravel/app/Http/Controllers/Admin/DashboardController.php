<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\Package;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                ['Paket', Package::active()->count(), Package::count().' total'],
                ['Frame aktif', Frame::active()->count(), Frame::count().' total'],
                ['Galeri', GalleryItem::active()->count(), GalleryItem::count().' total'],
            ],
            'latest' => GalleryItem::latest()->take(6)->get(),
        ]);
    }
}
