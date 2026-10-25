<?php

namespace App\Models;

use App\Support\Palette;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'label', 'type', 'size', 'color', 'image_path', 'sort_order', 'is_active'])]
class Frame extends Model
{
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

    public function getColorHexAttribute(): string
    {
        return Palette::normalize($this->color);
    }

    public function getTextColorAttribute(): string
    {
        return Palette::textOn($this->color);
    }
}
