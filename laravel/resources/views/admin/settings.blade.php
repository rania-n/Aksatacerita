@extends('layouts.admin')
@section('title', 'Info Studio')
@section('content')
<form class="panel admin-form admin-wide" method="post" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <div class="panel-head"><div><span>PROFIL STUDIO</span><h2>Info Studio</h2></div></div>
    @php
        $fields = [
            ['studio_name', 'Nama studio / lokasi', 'input', null],
            ['address', 'Alamat', 'textarea', null],
            ['open_hours', 'Jam buka', 'input', 'Contoh: 10.00 — 22.00 WIB'],
            ['last_session', 'Sesi terakhir', 'input', 'Contoh: Last session 21.30'],
            ['ticker', 'Teks pita di atas website', 'input', null],
            ['whatsapp', 'Nomor WhatsApp', 'input', 'Angka saja dengan kode negara, contoh 6285117375305'],
            ['instagram', 'Username Instagram', 'input', 'Tanpa @'],
            ['maps_url', 'Link Google Maps', 'input', null],
        ];
    @endphp
    @foreach ($fields as [$key, $label, $type, $hint])
        <label>{{ $label }}
            @if ($type === 'textarea')<textarea name="{{ $key }}" rows="3" required>{{ old($key, $s[$key]) }}</textarea>
            @else<input name="{{ $key }}" value="{{ old($key, $s[$key]) }}" required>@endif
            @if ($hint)<span class="hint">{{ $hint }}</span>@endif
            @error($key)<span class="error">{{ $message }}</span>@enderror
        </label>
    @endforeach
    <button class="btn btn-primary" type="submit">Simpan info <x-icon name="arrow" /></button>
</form>
@endsection
