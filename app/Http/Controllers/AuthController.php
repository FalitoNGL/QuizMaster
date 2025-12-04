<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Arahkan User ke Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Handle Callback dari Google
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Cari user berdasarkan Google ID atau Email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Jika user ada, update data terbaru (foto/nama) dan Login
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'name' => $googleUser->name
                ]);
                
                Auth::login($user);
            } else {
                // Jika user baru, buat akun baru
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => null, // Tidak butuh password
                ]);
                
                Auth::login($newUser);
            }

            // Redirect ke Menu Utama setelah login sukses
            return redirect()->route('menu')->with('success', 'Berhasil Login sebagai ' . Auth::user()->name);

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal Login Google: ' . $e->getMessage());
        }
    }

    // 3. Tampilkan Halaman Login Custom
    public function showLogin()
    {
        return view('auth.login');
    }

    // 4. Logout
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Berhasil Logout.');
    }
}