<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use App\Models\Setting;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'packages' => Package::active()->get(),
            'frames' => Frame::active()->get(),
            'gallery' => GalleryItem::active()->get(),
            'instagram' => InstagramPost::active()->get(),
            's' => Setting::allValues(),
            'categories' => Setting::list('gallery_categories'),
        ]);
    }
}
