<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'label', 'color', 'image_path', 'sort_order', 'is_active'])]
class Frame extends Model
{
    public const COLORS = ['blue' => 'Biru', 'orange' => 'Oranye', 'green' => 'Hijau', 'pink' => 'Pink'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image_path);
    }
}
