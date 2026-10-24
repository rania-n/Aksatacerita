<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstagramController extends Controller
{
    public function index(?InstagramPost $post = null)
    {
        return view('admin.instagram', [
            'items' => InstagramPost::orderBy('sort_order')->orderBy('id')->get(),
            'editing' => $post,
        ]);
    }

    public function store(Request $request)
    {
        InstagramPost::create($this->data($request) + ['sort_order' => (InstagramPost::max('sort_order') ?? -1) + 1]);

        return redirect()->route('admin.instagram.index')->with('ok', 'Postingan ditambahkan.');
    }

    public function update(Request $request, InstagramPost $post)
    {
        $post->update($this->data($request, $post));

        return redirect()->route('admin.instagram.index')->with('ok', 'Postingan diperbarui.');
    }

    public function destroy(InstagramPost $post)
    {
        $post->delete();

        return redirect()->route('admin.instagram.index')->with('ok', 'Postingan dihapus.');
    }

    private function data(Request $request, ?InstagramPost $current = null): array
    {
        $v = $request->validate(['url' => ['required', 'string', 'max:300']]);

        $url = InstagramPost::normalize($v['url'])
            ?? throw ValidationException::withMessages(['url' => 'Tempel link postingan Instagram, contoh: https://www.instagram.com/p/AbC123/']);

        $exists = InstagramPost::where('url', $url)->when($current, fn ($q) => $q->whereKeyNot($current->id))->exists();
        if ($exists) {
            throw ValidationException::withMessages(['url' => 'Postingan ini sudah ada.']);
        }

        return ['url' => $url, 'is_active' => $request->boolean('is_active')];
    }
}
