<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/** Hapus satu pilihan cepat (label, teks harga, kategori) dari daftar. */
class OptionController extends Controller
{
    public const LISTS = [
        'package_tag' => 'options_package_tag',
        'package_note' => 'options_package_note',
        'frame_label' => 'options_frame_label',
        'gallery_category' => 'gallery_categories',
    ];

    public function destroy(Request $request, string $list)
    {
        abort_unless(isset(self::LISTS[$list]), 404);

        $value = $request->validate(['value' => ['required', 'string', 'max:60']])['value'];
        Setting::removeFromList(self::LISTS[$list], $value);

        return response()->noContent();
    }
}
