<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index(?GalleryItem $item = null)
    {
        return view('admin.gallery', [
            'items' => GalleryItem::orderBy('sort_order')->orderBy('id')->get(),
            'editing' => $item,
        ]);
    }

    public function store(Request $request)
    {
        $v = $this->validated($request, imageRequired: true);
        GalleryItem::create([
            'title' => trim($v['title']),
            'category' => $v['category'],
            'image_path' => ImageUploader::store($request->file('image'), 'gallery', 1600),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (GalleryItem::max('sort_order') ?? -1) + 1,
        ]);

        return redirect()->route('admin.gallery.index')->with('ok', 'Foto ditambahkan.');
    }

    public function update(Request $request, GalleryItem $item)
    {
        $v = $this->validated($request, imageRequired: false, current: $item->category);
        $data = ['title' => trim($v['title']), 'category' => $v['category'], 'is_active' => $request->boolean('is_active')];
        if ($request->hasFile('image')) {
            Media::delete($item->image_path);
            $data['image_path'] = ImageUploader::store($request->file('image'), 'gallery', 1600);
        }
        $item->update($data);

        return redirect()->route('admin.gallery.index')->with('ok', 'Foto diperbarui.');
    }

    public function destroy(GalleryItem $item)
    {
        Media::delete($item->image_path);
        $item->delete();

        return redirect()->route('admin.gallery.index')->with('ok', 'Foto dihapus.');
    }

    private function validated(Request $request, bool $imageRequired, ?string $current = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'category' => ['required', Rule::in(array_filter([...GalleryItem::categories(), $current]))],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);
    }
}
