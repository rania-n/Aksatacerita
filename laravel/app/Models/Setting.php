<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public const DEFAULTS = [
        'studio_name' => 'Ruma Pinara',
        'address' => "Jl. I Gusti Ngurah Rai Gg. 8 No. 90,\nJepun, Tulungagung, Jawa Timur",
        'open_hours' => '10.00 — 22.00 WIB',
        'last_session' => 'Last session 21.30',
        'whatsapp' => '6285117375305',
        'instagram' => 'aksatacerita',
        'maps_url' => 'https://maps.google.com/?q=Ruma+Pinara+Tulungagung',
        'ticker' => 'STUDIO OPEN DAILY · 10.00—22.00 WIB',
    ];

    public static function get(string $key): ?string
    {
        return static::all()->pluck('value', 'key')[$key] ?? static::DEFAULTS[$key] ?? null;
    }

    /** Semua setting (dengan default) sebagai array. */
    public static function allValues(): array
    {
        return array_merge(static::DEFAULTS, static::all()->pluck('value', 'key')->filter(fn ($v) => $v !== null)->all());
    }
}
