<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Support\Palette;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public const TAG_SUGGESTIONS = ['Most Picked', 'Best Seller', 'Promo', 'Baru', 'Hemat'];

    public function index()
    {
        return view('admin.packages.index', ['items' => Package::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create()
    {
        return $this->form(null);
    }

    public function edit(Package $package)
    {
        return $this->form($package);
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

    private function form(?Package $package)
    {
        $labels = Package::whereNotNull('tag')->where('tag', '!=', '')->pluck('tag')->all();
        $notes = Package::whereNotNull('price_note')->where('price_note', '!=', '')->pluck('price_note')->all();
        $units = Package::whereNotNull('price_unit')->where('price_unit', '!=', '')->pluck('price_unit')->all();

        return view('admin.packages.form', [
            'editing' => $package,
            'package' => $package ?? new Package(['price_style' => 'k', 'duration_unit' => 'menit', 'price_note' => 'Mulai dari', 'color' => Palette::DEFAULT, 'is_active' => true]),
            'swatches' => Palette::swatches(),
            'tagSuggestions' => array_values(array_unique([...self::TAG_SUGGESTIONS, ...$labels])),
            'noteSuggestions' => array_values(array_unique(['Mulai dari', 'Hanya', 'Per sesi', ...$notes])),
            'unitSuggestions' => array_values(array_unique(['/ sesi', '/ orang', '/ strip', ...$units])),
        ]);
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
        // "35.000", "Rp 35000" -> 35000
        foreach (['price', 'old_price'] as $key) {
            $request->merge([$key => preg_replace('/\D+/', '', (string) $request->input($key)) ?: null]);
        }

        $v = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:120'],
            'duration_value' => ['nullable', 'integer', 'min:1', 'max:999'],
            'duration_unit' => ['required', 'in:menit,detik'],
            'price_note' => ['nullable', 'string', 'max:30'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'price_style' => ['required', 'in:k,rp'],
            'price_unit' => ['nullable', 'string', 'max:20'],
            'old_price' => ['nullable', 'integer', 'gt:price', 'max:100000000'],
            'tag' => ['nullable', 'string', 'max:40'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'features' => ['nullable', 'string', 'max:1000'],
        ], [
            'old_price.gt' => 'Harga coret harus lebih besar dari harga.',
            'color.regex' => 'Pilih warna kartu.',
        ]);

        return [
            'name' => trim($v['name']),
            'description' => $v['description'] ?? null,
            'duration_value' => $v['duration_value'] ?? null,
            'duration_unit' => $v['duration_unit'],
            'price_note' => $v['price_note'] ?? null,
            'price_amount' => (int) $v['price'],
            'price_style' => $v['price_style'],
            'price_unit' => $v['price_unit'] ?? null,
            'old_price_amount' => isset($v['old_price']) ? (int) $v['old_price'] : null,
            'tag' => $v['tag'] ?? null,
            'color' => strtolower($v['color']),
            'is_featured' => $request->boolean('is_featured'),
            'features' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $v['features'] ?? '')))),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
