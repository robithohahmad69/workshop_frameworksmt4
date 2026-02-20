<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    // Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback dari Google
    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id_google' => $googleUser->id,
                'password' => bcrypt(str()->random(16)),
            ]);
            session(['google_new_user' => true]); // ← tandai user baru
        } else {
            $user->update(['id_google' => $googleUser->id]);
        }

        // Generate OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Kirim OTP ke email
        Mail::raw("Kode OTP kamu adalah: $otp (berlaku 5 menit)", function ($message) use ($user) {
            $message->to($user->email)->subject('Kode OTP Login');
        });

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.form');
    }

    // Tampilkan form OTP
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    // Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'Sesi tidak valid.']);
        }

        if ($user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa.']);
        }

        $user->update(['otp' => null, 'otp_expires_at' => null]);
        session()->forget('otp_user_id');
        Auth::login($user);

        // User baru dari Google → set password dulu
        if (session('google_new_user')) {
            session()->forget('google_new_user');
            return redirect()->route('set.password');
        }

        return redirect('/');
    }

    // Tampilkan form set password
    public function showSetPasswordForm()
    {
        return view('auth.set-password');
    }

    // Simpan password baru
    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/');
    }
}