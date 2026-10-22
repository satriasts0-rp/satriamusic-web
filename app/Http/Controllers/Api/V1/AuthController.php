<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // 1. FUNGSI REGISTER (DAFTAR AKUN BARU)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Bikin Kunci Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Berhasil mendaftar! Selamat datang bro.',
            'data' => $user,
            'token' => $token
        ], 201);
    }

    // 2. FUNGSI LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah email ada dan passwordnya bener
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah nih bro.'],
            ]);
        }

        // Bikin Kunci Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'data' => $user,
            'token' => $token
        ]);
    }

    // 3. FUNGSI LOGOUT
    public function logout(Request $request)
    {
        // Hapus token yang lagi dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil. Sampai jumpa lagi!'
        ]);
    }

    // 4. FUNGSI RESET PASSWORD (VERSI LOKAL BYPASS)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|string|min:8',
        ], [
            'email.exists' => 'Bro, email ini belum terdaftar di database kita.'
        ]);

        // Cari user berdasarkan email, lalu timpa password lamanya
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil direset! Silakan login pakai password baru.'
        ]);
    }
}