@props(['type', 'item', 'first' => false, 'last' => false])
<span class="reorder">
    <span class="drag-handle" draggable="true" data-sort-url="{{ route('admin.reorder', [$type, $item->id, 'to']) }}" title="Tahan lalu geser untuk mengubah urutan" aria-hidden="true">⠿</span>
    <span class="arrows">
        <form method="post" action="{{ route('admin.reorder', [$type, $item->id, 'up']) }}">@csrf<button aria-label="Naikkan" @disabled($first)><x-icon name="up" :size="15" /></button></form>
        <form method="post" action="{{ route('admin.reorder', [$type, $item->id, 'down']) }}">@csrf<button aria-label="Turunkan" @disabled($last)><x-icon name="down" :size="15" /></button></form>
    </span>
</span>
