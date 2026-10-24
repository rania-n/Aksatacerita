@props(['name', 'size' => 20])
@php
$paths = [
    'arrow' => '<path d="M5 12h14M13 6l6 6-6 6" />',
    'camera' => '<path d="M4 7h3l2-3h6l2 3h3v12H4z" /><circle cx="12" cy="13" r="4" />',
    'clock' => '<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />',
    'grid' => '<rect x="4" y="4" width="6" height="6" /><rect x="14" y="4" width="6" height="6" /><rect x="4" y="14" width="6" height="6" /><rect x="14" y="14" width="6" height="6" />',
    'map' => '<path d="M12 22s7-6.3 7-13a7 7 0 1 0-14 0c0 6.7 7 13 7 13z" /><circle cx="12" cy="9" r="2.5" />',
    'menu' => '<path d="M4 7h16M4 12h16M4 17h16" />',
    'plus' => '<path d="M12 5v14M5 12h14" />',
    'sparkle' => '<path d="M12 2l1.7 6.3L20 10l-6.3 1.7L12 18l-1.7-6.3L4 10l6.3-1.7z" />',
    'trash' => '<path d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13" />',
    'edit' => '<path d="M4 20h4L19 9l-4-4L4 16z" /><path d="M13 7l4 4" />',
    'whatsapp' => '<path d="M20 11.5A8.5 8.5 0 0 1 7.4 19l-4.4 1 1.1-4.2A8.5 8.5 0 1 1 20 11.5z" /><path d="M8 8c.5 3 2 4.5 5 5" />',
    'up' => '<path d="M6 15l6-6 6 6" />',
    'down' => '<path d="M6 9l6 6 6-6" />',
    'user' => '<circle cx="12" cy="8" r="4" /><path d="M4 21c0-4 4-6 8-6s8 2 8 6" />',
    'x' => '<path d="M6 6l12 12M18 6L6 18" />',
];
@endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
