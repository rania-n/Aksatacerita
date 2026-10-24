<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Pengaturan yang berupa saklar (checkbox). */
    private const TOGGLES = ['proof_show', 'show_frames', 'show_booth', 'show_instagram'];

    public function edit()
    {
        return view('admin.settings', ['s' => Setting::allValues()]);
    }

    public function update(Request $request)
    {
        $request->merge([
            'whatsapp' => $this->normalizeWhatsapp((string) $request->input('whatsapp')),
            'instagram' => $this->normalizeHandle((string) $request->input('instagram'), 'instagram.com'),
            'tiktok' => $this->normalizeHandle((string) $request->input('tiktok'), 'tiktok.com'),
            'latitude' => $this->normalizeCoordinate($request->input('latitude')),
            'longitude' => $this->normalizeCoordinate($request->input('longitude')),
        ]);

        $v = $request->validate([
            'studio_name' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:300'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'maps_url' => ['nullable', 'url:http,https', 'max:500'],
            'open_days' => ['required', 'string', 'max:40'],
            'open_hours' => ['required', 'string', 'max:60'],
            'last_session' => ['nullable', 'string', 'max:60'],
            'ticker' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['required', 'regex:/^[1-9][0-9]{7,14}$/'],
            'wa_message' => ['required', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:100'],
            'instagram' => ['required', 'regex:/^[A-Za-z0-9._]{1,30}$/'],
            'tiktok' => ['nullable', 'regex:/^[A-Za-z0-9._]{1,30}$/'],
            'facebook' => ['nullable', 'url:http,https', 'max:200'],
            'hero_line1' => ['required', 'string', 'max:40'],
            'hero_line2' => ['required', 'string', 'max:40'],
            'hero_highlight' => ['required', 'string', 'max:30'],
            'hero_text' => ['required', 'string', 'max:300'],
            'proof_rating' => ['nullable', 'string', 'max:20'],
            'proof_text' => ['nullable', 'string', 'max:80'],
            'gallery_categories' => ['required', 'string', 'max:200'],
            'meta_title' => ['required', 'string', 'max:70'],
            'meta_description' => ['required', 'string', 'max:200'],
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Contoh: 085117375305 atau 6285117375305.',
            'instagram.regex' => 'Username Instagram hanya boleh huruf, angka, titik, dan underscore.',
            'tiktok.regex' => 'Username TikTok hanya boleh huruf, angka, titik, dan underscore.',
        ]);

        foreach (self::TOGGLES as $toggle) {
            $v[$toggle] = $request->boolean($toggle) ? '1' : '0';
        }
        $v['gallery_categories'] = implode(', ', array_slice(Setting::parseList($v['gallery_categories']), 0, 8));

        Setting::put(array_map(fn ($value) => is_string($value) ? trim($value) : $value, $v));

        return redirect()->route('admin.settings.edit')->with('ok', 'Info studio disimpan.');
    }

    /** 0812-3456 / +62 812 3456 / 62812... -> 62812... */
    private function normalizeWhatsapp(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }

    /** "@nama", "instagram.com/nama/?x=1" -> "nama" */
    private function normalizeHandle(string $value, string $host): string
    {
        $value = trim($value);
        $value = preg_replace('~^(https?://)?(www\.)?'.preg_quote($host, '~').'/~i', '', $value) ?? $value;
        $value = preg_split('~[/?#]~', $value)[0] ?? '';

        return ltrim($value, '@');
    }

    /** "-8,0654" -> "-8.0654" (null bila kosong) */
    private function normalizeCoordinate(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : str_replace(',', '.', $value);
    }
}
