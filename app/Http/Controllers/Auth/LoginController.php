<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request){
    // Validasi input dari form
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Mencari user berdasarkan username
    $user = User::where('username', $request->username)->first();

    // Cek apakah user ditemukan dan password cocok
    if ($user && Hash::check($request->password, $user->password)) {
        // Login user dan simpan sesi
        Auth::login($user);

        // Redirect ke halaman dashboard setelah login
        return redirect()->route('dashboard');
    } else {
        // Jika login gagal, kirim pesan error
        return back()->withErrors(['error' => 'Username atau password salah.']);
    }
}
}