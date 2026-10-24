@extends('layouts.admin')
@section('title', $editing ? 'Ubah Paket' : 'Tambah Paket')
@section('desc', 'Isi data paket. Kartu pratinjau menunjukkan persis tampilannya di website dan berubah langsung saat Anda mengetik.')
@section('content')
@php $e = $editing; $p = $package; @endphp
<a class="back-link" href="{{ route('admin.packages.index') }}">← Kembali ke daftar paket</a>
<form class="form-page" method="post" action="{{ $e ? route('admin.packages.update', $e) : route('admin.packages.store') }}">
    @csrf @if ($e) @method('PUT') @endif
    <div class="panel admin-form">
        <div class="panel-head"><div><span>{{ $e ? 'UBAH PAKET' : 'PAKET BARU' }}</span><h2>{{ $e ? $e->name : 'Isi data paket' }}</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>

        <label>Nama paket <i class="req">*</i><input name="name" value="{{ old('name', $p->name) }}" required maxlength="80" placeholder="Contoh: Bestie Session">@error('name')<span class="error">{{ $message }}</span>@enderror</label>

        <label>Deskripsi singkat<input name="description" value="{{ old('description', $p->description) }}" maxlength="120" placeholder="Contoh: Cocok untuk berdua atau bertiga"><span class="hint">Satu kalimat pendek tepat di bawah nama paket. Berbeda dengan "Isi paket" di bawah yang berupa daftar centang.</span>@error('description')<span class="error">{{ $message }}</span>@enderror</label>

        <div class="field">
            <span class="lbl">Durasi sesi</span>
            <div class="inline nowrap">
                <input type="number" name="duration_value" value="{{ old('duration_value', $p->duration_value) }}" min="1" max="999" inputmode="numeric" placeholder="15" aria-label="Lama sesi">
                <x-seg name="duration_unit" :options="\App\Models\Package::DURATION_UNITS" :value="$p->duration_unit" />
            </div>
            <span class="hint">Cukup ketik angkanya, tulisan "menit" atau "detik" ditambahkan otomatis. Kosongkan bila tidak perlu.</span>
            @error('duration_value')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <span class="lbl">Harga <i class="req">*</i></span>
            <div class="inline nowrap">
                <span class="input-group"><b>Rp</b><input type="number" name="price" id="f-price" value="{{ old('price', $p->price_amount) }}" required min="0" step="500" inputmode="numeric" placeholder="35000" aria-label="Harga"></span>
                <x-seg name="price_style" :options="['k' => $p->price_amount ? 'Rp'.\App\Support\Money::format($p->price_amount, 'k') : 'Rp35K', 'rp' => $p->price_amount ? 'Rp'.\App\Support\Money::format($p->price_amount, 'rp') : 'Rp35.000']" :value="$p->price_style" />
            </div>
            <span class="hint">Ketik angkanya saja (contoh 35000), lalu pilih cara menulisnya. Contoh tulisan ikut berubah sesuai angka.</span>
            @error('price')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label>Harga coret (promo)
            <span class="input-group"><b>Rp</b><input type="number" name="old_price" value="{{ old('old_price', $p->old_price_amount) }}" min="0" step="500" inputmode="numeric" placeholder="75000"></span>
            <span class="hint">Isi bila sedang diskon, harus lebih besar dari harga. Tampil dicoret di bawah harga.</span>@error('old_price')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label>Teks di atas harga<input name="price_note" id="f-note" value="{{ old('price_note', $p->price_note) }}" maxlength="30" placeholder="Mulai dari"></label>
        <x-chips target="f-note" list="package_note" :items="$noteOptions" />

        <label>Label<input name="tag" id="f-tag" value="{{ old('tag', $p->tag) }}" maxlength="40" placeholder="Contoh: Most Picked"><span class="hint">Klik pilihan di bawah atau ketik sendiri. Yang Anda ketik akan tersimpan jadi pilihan baru. Tekan × untuk menghapus pilihan.</span></label>
        <x-chips target="f-tag" list="package_tag" :items="$tagOptions" />

        <div class="field">
            <span class="lbl">Warna kartu <i class="req">*</i></span>
            <x-swatches name="color" :value="$p->color" :swatches="$swatches" />
            <span class="hint">Klik warna. Tombol "+" untuk memilih warna lain.</span>
            @error('color')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label>Isi paket<textarea name="features" rows="5" maxlength="1000" placeholder="Satu poin per baris">{{ old('features', implode("\n", $p->features ?? [])) }}</textarea><span class="hint">Daftar centang di kartu. Satu poin per baris, contoh: "2 strip cetak".</span>@error('features')<span class="error">{{ $message }}</span>@enderror</label>

        <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $p->is_featured))> Paket unggulan <small>(kartu menonjol saat halaman dibuka; hanya satu)</small></label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $p->is_active ?? true))> Tampilkan di website</label>

        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Publikasikan' }} <x-icon name="arrow" /></button>
        <a class="btn-ghost" href="{{ route('admin.packages.index') }}">Batal</a>
    </div>

    <div class="form-preview">
        <span class="preview-label">PRATINJAU DI WEBSITE</span>
        <div class="preview-box" data-preview="package">
            <x-package-card :pkg="$p->name ? $p : new \App\Models\Package(['name' => 'Nama paket', 'price_amount' => 35000, 'price_style' => 'k', 'price_note' => 'Mulai dari', 'color' => $p->color, 'features' => ['Isi paket pertama', 'Isi paket kedua']])" :active="true" :preview="true" />
        </div>
    </div>
</form>
@endsection
