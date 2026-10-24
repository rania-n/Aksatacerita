@props(['name' => 'color', 'value', 'swatches'])
@php $value = \App\Support\Palette::normalize($value); $known = in_array($value, $swatches, true); @endphp
{{-- Pilih warna sekali klik, atau "+" untuk warna bebas --}}
<div class="swatches" data-swatches>
    <input type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
    @foreach ($swatches as $hex)
        <button type="button" class="swatch {{ $hex === $value ? 'on' : '' }}" data-value="{{ $hex }}" style="background:{{ $hex }}" title="{{ \App\Support\Palette::name($hex) }}" aria-label="{{ \App\Support\Palette::name($hex) }}"></button>
    @endforeach
    <label class="swatch-custom {{ $known ? '' : 'on' }}" title="Warna lain"><input type="color" value="{{ $value }}" aria-label="Pilih warna lain"><span>+</span></label>
</div>
