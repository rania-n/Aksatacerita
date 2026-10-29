@props(['type', 'item', 'first' => false, 'last' => false, 'edit', 'destroy', 'confirm'])
<div class="row-actions">
    <input type="checkbox" class="bulk-check" name="ids[]" value="{{ $item->id }}" form="bulk-form" aria-label="Pilih item">
    <x-reorder :type="$type" :item="$item" :first="$first" :last="$last" />
    <a class="act" href="{{ $edit }}"><x-icon name="edit" :size="14" /> Ubah</a>
    <form method="post" action="{{ $destroy }}" onsubmit="return confirm({{ Illuminate\Support\Js::from($confirm) }})">@csrf @method('DELETE')<button class="act danger"><x-icon name="trash" :size="14" /> Hapus</button></form>
</div>
