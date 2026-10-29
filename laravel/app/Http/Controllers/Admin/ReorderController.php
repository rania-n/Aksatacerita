<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/** Ubah urutan item di daftar admin: naik/turun satu langkah, atau langsung loncat ke nomor posisi. */
class ReorderController extends Controller
{
    private const TYPES = [
        'paket' => [Package::class, 'admin.packages.index'],
        'frame' => [Frame::class, 'admin.frames.index'],
        'galeri' => [GalleryItem::class, 'admin.gallery.index'],
        'instagram' => [InstagramPost::class, 'admin.instagram.index'],
    ];

    public function __invoke(Request $request, string $type, int $id, string $direction)
    {
        abort_unless(isset(self::TYPES[$type]) && in_array($direction, ['up', 'down', 'to'], true), 404);

        [$class, $route] = self::TYPES[$type];
        /** @var class-string<Model> $class */
        $ids = $class::orderBy('sort_order')->orderBy('id')->pluck('id')->all();
        $from = array_search($id, $ids, true);
        abort_if($from === false, 404);

        $to = match ($direction) {
            'up' => $from - 1,
            'down' => $from + 1,
            default => max(0, min(count($ids) - 1, $request->integer('position', $from + 1) - 1)),
        };

        if ($to !== $from && isset($ids[$to])) {
            array_splice($ids, $to, 0, array_splice($ids, $from, 1));
            foreach ($ids as $position => $itemId) {
                $class::whereKey($itemId)->update(['sort_order' => $position]);
            }
        }

        return redirect()->route($route);
    }
}
