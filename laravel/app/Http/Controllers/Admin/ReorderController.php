<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\GalleryItem;
use App\Models\InstagramPost;
use App\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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

    public function __invoke(Request $request, string $type, int $id, string $direction)
    {
        abort_unless(isset(self::TYPES[$type]) && in_array($direction, ['up', 'down', 'to'], true), 404);

        [$class, $route] = self::TYPES[$type];
        /** @var class-string<Model> $class */
        $ids = $class::orderBy('sort_order')->orderBy('id')->pluck('id')->all();
        $from = array_search($id, $ids, true);
        abort_if($from === false, 404);

        if ($direction === 'to') {
            $to = max(0, min(count($ids) - 1, (int) $request->input('position', 1) - 1));
            array_splice($ids, $to, 0, array_splice($ids, $from, 1));
        } else {
            $to = $direction === 'up' ? $from - 1 : $from + 1;
            if (isset($ids[$to])) {
                [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];
            }
        }

        if ($to !== $from) {
            foreach ($ids as $position => $itemId) {
                $class::whereKey($itemId)->update(['sort_order' => $position]);
            }
        }

        return $request->expectsJson() ? response()->noContent() : redirect()->to(route($route).'#item-'.$type.'-'.$id);
    }
}
