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
        Package::create($this->data($request) + ['sort_order' => (Package::max('sort_order') ?? -1) + 1]);

        return redirect()->route('admin.packages.index')->with('ok', 'Paket ditambahkan.');
    }

    public function update(Request $request, Package $package)
    {
        $package->update($this->data($request));

        return redirect()->route('admin.packages.index')->with('ok', 'Paket diperbarui.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('ok', 'Paket dihapus.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'string', 'max:20'],
            'tag' => ['nullable', 'string', 'max:40'],
            'color' => ['required', Rule::in(array_keys(Package::COLORS))],
            'features' => ['nullable', 'string', 'max:1000'],
        ]);

        return [
            'name' => trim($v['name']),
            'price' => preg_replace('/^rp\.?\s*/i', '', trim($v['price'])),
            'tag' => $v['tag'] ?? null,
            'color' => $v['color'],
            'features' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $v['features'] ?? '')))),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
