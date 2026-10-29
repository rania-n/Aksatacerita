<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use Illuminate\Database\Eloquent\Model;

/** Geser urutan item naik/turun di daftar admin; setelahnya halaman kembali tepat ke item tersebut. */
class ReorderController extends Controller
{
    /** Jenis daftar => [model, rute daftar]. Dipakai juga oleh aksi massal. */
    public const TYPES = [
        'paket' => [Package::class, 'admin.packages.index'],
        'frame' => [Frame::class, 'admin.frames.index'],
        'galeri' => [GalleryItem::class, 'admin.gallery.index'],
        'instagram' => [InstagramPost::class, 'admin.instagram.index'],
    ];

    public function __invoke(string $type, int $id, string $direction)
    {
        abort_unless(isset(self::TYPES[$type]) && in_array($direction, ['up', 'down'], true), 404);

        [$class, $route] = self::TYPES[$type];
        /** @var class-string<Model> $class */
        $ids = $class::orderBy('sort_order')->orderBy('id')->pluck('id')->all();
        $from = array_search($id, $ids, true);
        abort_if($from === false, 404);

        $to = $direction === 'up' ? $from - 1 : $from + 1;
        if (isset($ids[$to])) {
            [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];
            foreach ($ids as $position => $itemId) {
                $class::whereKey($itemId)->update(['sort_order' => $position]);
            }
        }

        return redirect()->to(route($route).'#item-'.$type.'-'.$id);
    }
}
