<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
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
                ['Instagram', InstagramPost::active()->count(), InstagramPost::count().' total'],
            ],
            'defaultLogin' => auth()->user()->email === 'admin@aksatacerita.test',
            'latest' => GalleryItem::latest()->take(6)->get(),
        ]);
    }
}
