<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\Media;
use App\Models\Setting;
use App\Support\ImageUploader;
use App\Support\Palette;
use Illuminate\Http\Request;

class FrameController extends Controller
{
    public function index()
    {
        return view('admin.frames.index', ['items' => Frame::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create()
    {
        return $this->form(null);
    }

    public function edit(Frame $frame)
    {
        return $this->form($frame);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $data['sort_order'] = (Frame::max('sort_order') ?? -1) + 1;
        Frame::create($data + ['color' => Palette::DEFAULT]);

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

    private function form(?Frame $frame)
    {
        return view('admin.frames.form', [
            'editing' => $frame,
            'frame' => $frame ?? new Frame(['color' => Palette::DEFAULT, 'is_active' => true]),
            'labelOptions' => Setting::list('options_frame_label'),
        ]);
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'label' => ['nullable', 'string', 'max:20'],
            'image' => [$request->route('frame') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        Setting::addToList('options_frame_label', isset($v['label']) ? strtoupper($v['label']) : null);

        $data = [
            'name' => trim($v['name']),
            'label' => isset($v['label']) ? strtoupper($v['label']) : null,
            'is_active' => $request->boolean('is_active'),
        ];
        if ($request->hasFile('image')) {
            $data['image_path'] = ImageUploader::store($request->file('image'), 'frames', 1200);
        }

        return $data;
    }
}
