<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'price', 'tag', 'color', 'features', 'sort_order', 'is_active'])]
class Package extends Model
{
    public const COLORS = ['blue' => 'Biru', 'orange' => 'Oranye', 'pink' => 'Pink'];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
