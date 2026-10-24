@props(['variant' => 'footer'])
@php
    $c = config('aksata.credit');
    $names = collect($c['names'] ?? [])->filter()->implode(', ');
@endphp
<p class="credit credit-{{ $variant }}">
    <span class="credit-mark">✦</span>
    <span>{{ $c['prefix'] }} @if ($names)<b>{{ $names }}</b> · @endif<b>{{ $c['team'] }}</b></span>
</p>
