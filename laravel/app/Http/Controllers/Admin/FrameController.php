<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FrameController extends Controller
{
    public function index(?Frame $frame = null)
    {
        return view('admin.frames', [
            'items' => Frame::orderBy('sort_order')->orderBy('id')->get(),
            'editing' => $frame,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $data['sort_order'] = (Frame::max('sort_order') ?? -1) + 1;
        Frame::create($data);

        return redirect()->route('admin.frames.index')->with('ok', 'Frame ditambahkan.');
    }

    public function update(Request $request, Frame $frame)
    {
        $data = $this->data($request);
        if (isset($data['image_path'])) {
            Media::delete($frame->image_path);
        }
        $frame->update($data);

        return redirect()->route('admin.frames.index')->with('ok', 'Frame diperbarui.');
    }

    public function destroy(Frame $frame)
    {
        Media::delete($frame->image_path);
        $frame->delete();

        return redirect()->route('admin.frames.index')->with('ok', 'Frame dihapus.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'label' => ['nullable', 'string', 'max:20'],
            'color' => ['required', Rule::in(array_keys(Frame::COLORS))],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data = [
            'name' => trim($v['name']),
            'label' => isset($v['label']) ? strtoupper($v['label']) : null,
            'color' => $v['color'],
            'is_active' => $request->boolean('is_active'),
        ];
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('frames', 'public');
        }

        return $data;
    }
}
