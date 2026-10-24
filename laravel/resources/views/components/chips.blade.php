@props(['target', 'items', 'list'])
{{-- Klik untuk mengisi kolom teks (id = $target). × menghapus pilihan; nilai baru yang disimpan otomatis masuk daftar. --}}
<div class="chips" data-chips="{{ $target }}" data-list="{{ $list }}" aria-label="Pilihan cepat">
    @foreach ($items as $item)
        <span class="chip" data-value="{{ $item }}"><button type="button" data-value="{{ $item }}">{{ $item }}</button><button type="button" class="chip-x" aria-label="Hapus pilihan {{ $item }}" title="Hapus pilihan">×</button></span>
    @endforeach
</div>
