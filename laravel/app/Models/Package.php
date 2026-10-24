<?php

namespace App\Models;

use App\Support\Money;
use App\Support\Palette;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'duration_value', 'duration_unit', 'price_note', 'price_amount', 'price_style', 'old_price_amount', 'price_unit', 'tag', 'color', 'features', 'is_featured', 'sort_order', 'is_active'])]
class Package extends Model
{
    use HasFactory;

    public const DURATION_UNITS = ['menit' => 'Menit', 'detik' => 'Detik'];

    public const PRICE_STYLES = ['k' => 'Singkat (35K)', 'rp' => 'Lengkap (35.000)'];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'price_amount' => 'integer', 'old_price_amount' => 'integer', 'duration_value' => 'integer'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getPriceTextAttribute(): string
    {
        return Money::format($this->price_amount, $this->price_style ?? 'k');
    }

    public function getOldPriceTextAttribute(): ?string
    {
        return $this->old_price_amount ? Money::format($this->old_price_amount, $this->price_style ?? 'k') : null;
    }

    public function getDurationTextAttribute(): ?string
    {
        return $this->duration_value ? $this->duration_value.' '.($this->duration_unit ?: 'menit') : null;
    }

    public function getColorHexAttribute(): string
    {
        return Palette::normalize($this->color);
    }
}
