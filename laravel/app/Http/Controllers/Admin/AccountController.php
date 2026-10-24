<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function edit()
    {
        return view('admin.account');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $v = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user->name = trim($v['name']);
        $user->email = $v['email'];
        if (! empty($v['password'])) {
            $user->password = $v['password'];
        }
        $user->save();

        $request->session()->regenerate();

        return redirect()->route('admin.account.edit')->with('ok', 'Akun diperbarui.');
    }
}
