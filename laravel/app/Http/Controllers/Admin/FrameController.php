<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\Media;
use App\Models\Setting;
use App\Support\ImageUploader;
use App\Support\Palette;
use App\Support\SeedAssets;
use App\Support\UploadName;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        $files = Arr::wrap($request->file('image'));
        $nextOrder = (Frame::max('sort_order') ?? -1) + 1;

        foreach ($files as $i => $file) {
            $frame = Frame::create($data + [
                'name' => trim(count($files) > 1 ? $request->input("names.{$i}") : $request->input('name')),
                'image_path' => ImageUploader::store($file, 'frames', 1200),
                'sort_order' => $nextOrder + $i,
                'color' => Palette::DEFAULT,
            ]);
            SeedAssets::sync($frame->image_path, SeedAssets::frameFolder($frame->type), $frame->only(['name', 'label', 'size']));
        }

        return redirect()->route('admin.frames.index')->with('ok', count($files) > 1 ? count($files).' frame ditambahkan.' : 'Frame ditambahkan.');
    }

    public function update(Request $request, Frame $frame)
    {
        $data = $this->data($request) + ['name' => trim($request->input('name'))];
        if ($request->hasFile('image')) {
            $data['image_path'] = ImageUploader::store($request->file('image'), 'frames', 1200);
        }
        if (isset($data['image_path'])) {
            Media::delete($frame->image_path);
            SeedAssets::forget($frame->image_path);
        }
        $frame->update($data);
        SeedAssets::sync($frame->image_path, SeedAssets::frameFolder($frame->type), $frame->only(['name', 'label', 'size']));

        return redirect()->route('admin.frames.index')->with('ok', 'Frame diperbarui.');
    }

    public function destroy(Frame $frame)
    {
        Media::delete($frame->image_path);
        SeedAssets::forget($frame->image_path);
        $frame->delete();

        return redirect()->route('admin.frames.index')->with('ok', 'Frame dihapus.');
    }

    private function form(?Frame $frame)
    {
        return view('admin.frames.form', [
            'editing' => $frame,
            'frame' => $frame ?? new Frame(['color' => Palette::DEFAULT, 'is_active' => true]),
            'labelOptions' => Setting::list('options_frame_label'),
            'typeOptions' => Setting::list('options_frame_type'),
            'sizeOptions' => Setting::list('options_frame_size'),
        ]);
    }

    /** Data bersama (tanpa nama & gambar) untuk satu atau banyak frame. */
    private function data(Request $request): array
    {
        $creating = ! $request->route('frame');
        if ($creating) {
            UploadName::normalize($request);
        }
        $count = count(Arr::wrap($request->file('image')));
        $many = $creating && $count > 1;
        $v = $request->validate([
            'name' => [$many ? 'nullable' : 'required', 'string', 'max:60'],
            'names' => $many ? ['required', 'array', 'size:'.$count] : ['nullable', 'array'],
            'names.*' => ['required', 'string', 'max:60'],
            'label' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'string', 'max:30'],
            'size' => ['nullable', 'string', 'max:12'],
            'image' => $creating ? ['required', 'array', 'max:20'] : ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'image.*' => $creating ? ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'] : [],
        ], ['names.*.required' => 'Nama setiap frame wajib diisi.', 'names.size' => 'Jumlah nama harus sama dengan jumlah file.']);

        Setting::addToList('options_frame_label', isset($v['label']) ? strtoupper($v['label']) : null);
        Setting::addToList('options_frame_type', $v['type'] ?? null);
        Setting::addToList('options_frame_size', isset($v['size']) ? strtoupper($v['size']) : null);

        return [
            'label' => isset($v['label']) ? strtoupper($v['label']) : null,
            'type' => $v['type'] ?? null,
            'size' => isset($v['size']) ? strtoupper($v['size']) : null,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
