<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\SeedAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/** Aksi massal di daftar admin: tampilkan, sembunyikan, atau hapus banyak item sekaligus. */
class BulkController extends Controller
{
    public function __invoke(Request $request, string $type)
    {
        abort_unless(isset(ReorderController::TYPES[$type]), 404);
        abort_unless(in_array($request->input('action'), ['show', 'hide', 'delete'], true), 404);

        [$class, $route] = ReorderController::TYPES[$type];
        /** @var class-string<Model> $class */
        $ids = array_filter((array) $request->input('ids'), 'is_numeric');

        if ($ids === []) {
            return redirect()->route($route)->with('ok', 'Pilih minimal satu item dulu.');
        }

        $items = $class::whereIn('id', $ids)->get();

        if ($request->input('action') === 'delete') {
            $items->each(function (Model $item) {
                Media::delete($item->image_path);
                SeedAssets::forget($item->image_path);
                $item->delete();
            });
            $message = $items->count().' item dihapus.';
        } else {
            $class::whereIn('id', $items->modelKeys())->update(['is_active' => $request->input('action') === 'show']);
            $message = $items->count().($request->input('action') === 'show' ? ' item ditampilkan.' : ' item disembunyikan.');
        }

        return redirect()->route($route)->with('ok', $message);
    }
}
