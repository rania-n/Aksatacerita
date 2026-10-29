<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\Setting;
use App\Support\ImageUploader;
use App\Support\SeedAssets;
use App\Support\UploadName;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', ['items' => GalleryItem::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create()
    {
        return $this->form(null);
    }

    public function edit(GalleryItem $item)
    {
        return $this->form($item);
    }

    private function form(?GalleryItem $item)
    {
        return view('admin.gallery.form', [
            'editing' => $item,
            'categories' => array_values(array_unique(array_filter([...GalleryItem::categories(), $item?->category]))),
        ]);
    }

    public function store(Request $request)
    {
        $v = $this->validated($request, creating: true);
        $files = Arr::wrap($request->file('image'));
        $nextOrder = (GalleryItem::max('sort_order') ?? -1) + 1;

        foreach ($files as $i => $file) {
            $item = GalleryItem::create([
                'title' => UploadName::for($v['title'] ?? null, $file, $i, count($files), 80),
                'category' => $v['category'],
                'image_path' => ImageUploader::store($file, 'gallery', 1600),
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $nextOrder + $i,
            ]);
            SeedAssets::sync($item->image_path, SeedAssets::galleryFolder($item->category), $item->only(['title']));
        }

        return redirect()->route('admin.gallery.index')->with('ok', count($files) > 1 ? count($files).' foto ditambahkan.' : 'Foto ditambahkan.');
    }

    public function update(Request $request, GalleryItem $item)
    {
        $v = $this->validated($request, creating: false);
        $data = ['title' => trim($v['title']), 'category' => $v['category'], 'is_active' => $request->boolean('is_active')];
        if ($request->hasFile('image')) {
            Media::delete($item->image_path);
            SeedAssets::forget($item->image_path);
            $data['image_path'] = ImageUploader::store($request->file('image'), 'gallery', 1600);
        }
        $item->update($data);
        SeedAssets::sync($item->image_path, SeedAssets::galleryFolder($item->category), $item->only(['title']));

        return redirect()->route('admin.gallery.index')->with('ok', 'Foto diperbarui.');
    }

    public function destroy(GalleryItem $item)
    {
        Media::delete($item->image_path);
        SeedAssets::forget($item->image_path);
        $item->delete();

        return redirect()->route('admin.gallery.index')->with('ok', 'Foto dihapus.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $request->merge(['category' => trim((string) $request->input('category'))]);
        if ($creating) {
            UploadName::normalize($request);
        }

        $v = $request->validate([
            'title' => [$creating ? 'nullable' : 'required', 'string', 'max:80'],
            'category' => ['required', 'string', 'max:30'],
            'image' => $creating ? ['required', 'array', 'max:20'] : ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'image.*' => $creating ? ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'] : [],
        ]);

        Setting::addToList('gallery_categories', $v['category']);

        return $v;
    }
}
