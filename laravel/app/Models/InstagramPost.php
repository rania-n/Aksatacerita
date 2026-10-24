<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['url', 'sort_order', 'is_active'])]
class InstagramPost extends Model
{
    use HasFactory;

    public const URL_PATTERN = '~^https?://(?:www\.)?instagram\.com/(p|reel|tv)/([A-Za-z0-9_-]+)~i';

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Link postingan Instagram -> bentuk baku, atau null kalau bukan link postingan. */
    public static function normalize(string $url): ?string
    {
        if (! preg_match(self::URL_PATTERN, trim($url), $m)) {
            return null;
        }

        return 'https://www.instagram.com/'.strtolower($m[1]).'/'.$m[2].'/';
    }
}
