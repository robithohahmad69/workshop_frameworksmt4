<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('vendor.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('vendor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('vendor.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function showRegister()
{
    return view('vendor.auth.register');
}

public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:vendors,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $vendor = \App\Models\Vendor::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::guard('vendor')->login($vendor);

    return redirect()->route('vendor.dashboard');
}

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login');
    }

    public function showProfile()
    {
        return view('vendor.profile');
    }

    public function updateProfile(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:vendors,email,' . $vendor->id,
            'password'         => 'nullable|min:6|confirmed',
        ]);

        $vendor->name  = $request->name;
        $vendor->email = $request->email;

        if ($request->filled('password')) {
            $vendor->password = Hash::make($request->password);
        }

        $vendor->save();

        return back()->with('success', 'Profile berhasil diupdate!');
    }
}