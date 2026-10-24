@props(['name', 'options', 'value'])
{{-- Pilihan satu klik (pengganti dropdown) --}}
<div class="seg" role="radiogroup">
    @foreach ($options as $key => $label)
        <label class="seg-opt"><input type="radio" name="{{ $name }}" value="{{ $key }}" @checked((string) old($name, $value) === (string) $key)><span data-opt="{{ $key }}">{{ $label }}</span></label>
    @endforeach
</div>
