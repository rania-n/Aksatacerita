<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/** Lupa / ganti password lewat link yang dikirim ke email admin. */
class PasswordResetController extends Controller
{
    private const SENT = 'Jika email terdaftar, link untuk membuat password baru sudah dikirim. Cek kotak masuk (dan folder spam). Link berlaku 60 menit.';

    public function request()
    {
        return view('auth.forgot-password');
    }

    /** Selalu menjawab sama, supaya tidak bisa dipakai menebak email admin. */
    public function email(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', self::SENT);
    }

    public function form(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], ['password.min' => 'Password baru minimal 8 karakter.']);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', 'Password berhasil diganti. Silakan masuk dengan password baru.');
    }

    /** Dari halaman Akun: kirim link ganti password ke email admin yang sedang masuk. */
    public function sendToSelf(Request $request)
    {
        Password::sendResetLink(['email' => $request->user()->email]);

        return back()->with('ok', 'Link ganti password dikirim ke '.$request->user()->email.'.');
    }
}
