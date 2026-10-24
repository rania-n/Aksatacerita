@props(['target', 'items'])
{{-- Klik sekali untuk mengisi kolom teks (id = $target) --}}
<div class="chips" data-chips="{{ $target }}" aria-label="Pilihan cepat">
    @foreach ($items as $item)<button type="button" data-value="{{ $item }}">{{ $item }}</button>@endforeach
</div>
