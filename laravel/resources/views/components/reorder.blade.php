@props(['type', 'item', 'first' => false, 'last' => false])
<span class="reorder">
    <form method="post" action="{{ route('admin.reorder', [$type, $item->id, 'up']) }}">@csrf<button aria-label="Naikkan" @disabled($first)><x-icon name="up" :size="15" /></button></form>
    <form method="post" action="{{ route('admin.reorder', [$type, $item->id, 'down']) }}">@csrf<button aria-label="Turunkan" @disabled($last)><x-icon name="down" :size="15" /></button></form>
    <form class="jump" method="post" action="{{ route('admin.reorder', [$type, $item->id, 'to']) }}" title="Loncat langsung ke nomor urut (1 = paling atas)">@csrf<input type="number" name="position" min="1" placeholder="#" required aria-label="Pindah ke nomor urut"><button aria-label="Pindah ke nomor urut">→</button></form>
</span>
