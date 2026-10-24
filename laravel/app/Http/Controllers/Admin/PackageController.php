<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index(?Package $package = null)
    {
        return view('admin.packages', [
            'items' => Package::orderBy('sort_order')->orderBy('id')->get(),
            'editing' => $package,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $this->clearFeatured($data);
        Package::create($data + ['sort_order' => (Package::max('sort_order') ?? -1) + 1]);

        return redirect()->route('admin.packages.index')->with('ok', 'Paket ditambahkan.');
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->data($request);
        $this->clearFeatured($data, $package);
        $package->update($data);

        return redirect()->route('admin.packages.index')->with('ok', 'Paket diperbarui.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('ok', 'Paket dihapus.');
    }

    /** Hanya satu paket yang boleh jadi "unggulan"; yang lama dilepas. */
    private function clearFeatured(array $data, ?Package $except = null): void
    {
        if ($data['is_featured']) {
            Package::when($except, fn ($q) => $q->whereKeyNot($except->id))->update(['is_featured' => false]);
        }
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:120'],
            'duration' => ['nullable', 'string', 'max:40'],
            'price_note' => ['nullable', 'string', 'max:30'],
            'price' => ['required', 'string', 'max:20'],
            'price_unit' => ['nullable', 'string', 'max:20'],
            'old_price' => ['nullable', 'string', 'max:20'],
            'tag' => ['nullable', 'string', 'max:40'],
            'color' => ['required', Rule::in(array_keys(Package::COLORS))],
            'features' => ['nullable', 'string', 'max:1000'],
        ]);

        $money = fn (?string $value): ?string => $value === null || trim($value) === '' ? null : preg_replace('/^rp\.?\s*/i', '', trim($value));

        return [
            'name' => trim($v['name']),
            'description' => $v['description'] ?? null,
            'duration' => $v['duration'] ?? null,
            'price_note' => $v['price_note'] ?? null,
            'price' => $money($v['price']),
            'price_unit' => $v['price_unit'] ?? null,
            'old_price' => $money($v['old_price'] ?? null),
            'tag' => $v['tag'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'color' => $v['color'],
            'features' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $v['features'] ?? '')))),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
