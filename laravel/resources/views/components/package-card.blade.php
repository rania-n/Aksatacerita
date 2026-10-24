@props(['pkg', 'active' => false, 'icon' => 'camera', 'preview' => false])
{{-- Kartu paket: dipakai di website dan di pratinjau admin (data-p = titik sambung pratinjau langsung) --}}
<article class="package-card {{ $active ? 'active' : '' }}" style="--card: {{ $pkg->color_hex }}">
    <div class="package-top"><span data-p="tag" {{ $pkg->tag ? '' : 'hidden' }}>{{ $pkg->tag }}</span><x-icon :name="$icon" :size="28" /></div>
    <div>
        <h3 data-p="name">{{ $pkg->name }}</h3>
        @if ($preview || $pkg->description)<p class="package-desc" data-p="description" {{ $pkg->description ? '' : 'hidden' }}>{{ $pkg->description }}</p>@endif
        @if ($preview || $pkg->price_note)<p data-p="note" {{ $pkg->price_note ? '' : 'hidden' }}>{{ $pkg->price_note }}</p>@endif
        <strong><small>Rp</small><span data-p="price">{{ $pkg->price_text }}</span></strong>
        @if ($preview || $pkg->old_price_text)<s class="package-old" data-p="old" {{ $pkg->old_price_text ? '' : 'hidden' }}>Rp{{ $pkg->old_price_text }}</s>@endif
    </div>
    @if ($preview || $pkg->duration_text)<div class="package-duration" data-p="duration" {{ $pkg->duration_text ? '' : 'hidden' }}><x-icon name="clock" :size="14" /> <span data-p="duration-text">{{ $pkg->duration_text }}</span></div>@endif
    <ul data-p="features">@foreach ($pkg->features ?? [] as $f)<li><span>✓</span>{{ $f }}</li>@endforeach</ul>
    @if ($preview)
        <button type="button" class="package-select" tabindex="-1">Pilih paket <x-icon name="arrow" /></button>
    @else
        <button class="package-select" data-open-booking data-session="{{ $pkg->name }}">Pilih paket <x-icon name="arrow" /></button>
    @endif
</article>
