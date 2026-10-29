@props(['type'])
<form id="bulk-form" class="bulk-bar" method="post" action="{{ route('admin.bulk', $type) }}" data-bulk>
    @csrf
    <label class="bulk-all-label"><input type="checkbox" class="bulk-all"> Pilih semua</label>
    <span class="bulk-count">Pilih item untuk aksi massal</span>
    <span class="bulk-actions">
        <button class="act" name="action" value="show" disabled>Tampilkan</button>
        <button class="act" name="action" value="hide" disabled>Sembunyikan</button>
        <button class="act danger" name="action" value="delete" disabled><x-icon name="trash" :size="14" /> Hapus</button>
    </span>
</form>
