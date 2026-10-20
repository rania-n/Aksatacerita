@props(['light' => false])
<a class="brand official-brand {{ $light ? 'brand-light' : '' }}" href="{{ url('/') }}" aria-label="Aksata Cerita"><span class="brand-crop"><img src="{{ asset('assets/aksata-logo.png') }}" alt="Aksata Cerita"></span></a>
