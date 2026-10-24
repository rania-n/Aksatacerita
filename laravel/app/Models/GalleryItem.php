<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'category', 'image_path', 'sort_order', 'is_active'])]
class GalleryItem extends Model
{
    /**
     * Daftar kategori galeri (diatur admin di Info Studio).
     *
     * @return list<string>
     */
    public static function categories(): array
    {
        return Setting::list('gallery_categories');
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): string
    {
        return Media::url($this->image_path);
    }
}
