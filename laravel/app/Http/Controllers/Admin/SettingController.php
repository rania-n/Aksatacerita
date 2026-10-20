<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['s' => Setting::allValues()]);
    }

    public function update(Request $request)
    {
        $v = $request->validate([
            'studio_name' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:300'],
            'open_hours' => ['required', 'string', 'max:60'],
            'last_session' => ['required', 'string', 'max:60'],
            'whatsapp' => ['required', 'regex:/^[0-9]{8,15}$/'],
            'instagram' => ['required', 'regex:/^[A-Za-z0-9._]{1,30}$/'],
            'maps_url' => ['required', 'url', 'max:300'],
            'ticker' => ['required', 'string', 'max:100'],
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp hanya angka dengan kode negara, contoh 6285117375305.',
            'instagram.regex' => 'Username Instagram tanpa @, hanya huruf, angka, titik, underscore.',
        ]);

        foreach ($v as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.edit')->with('ok', 'Info studio disimpan.');
    }
}
