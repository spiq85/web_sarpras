<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk logging

class AuthController extends Controller
{
    public function apiLogin(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!in_array($user->role, ['user', 'admin'])) {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true, // Tambahkan ini
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [ // Data user di root ini juga bagus
                    'users_id' => $user->users_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'class' => $user->class,
                    'major' => $user->major,
                    'created_at' => $user->created_at, // Tambahkan created_at
                    'updated_at' => $user->updated_at, // Tambahkan updated_at
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Username atau password salah'], 401); // Tambahkan success false
    }

    public function apiLogout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['success' => true, 'message' => 'Logout berhasil'], 200); // Tambahkan success true
        } catch (\Exception $e) {
            Log::error('Error during API logout:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal logout: ' . $e->getMessage()], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }

            return response()->json([
                'success' => true, // Tambahkan 'success'
                'message' => 'Data profil berhasil diambil.', // Tambahkan pesan
                'data' => [ // Bungkus data user di dalam kunci 'data'
                    'users_id' => $user->users_id,
                    'name' => $user->name, // Pastikan kolom 'name' ada di tabel 'users'
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'class' => $user->class,
                    'major' => $user->major,
                    'created_at' => $user->created_at, // Tambahkan created_at
                    'updated_at' => $user->updated_at, // Tambahkan updated_at
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user profile (API /me):', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil: ' . $e->getMessage()
            ], 500);
        }
    }

    // Metode login untuk web (tidak berubah)
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Login berhasil, selamat datang di halaman admin.');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman admin.');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau Password salah.');
        }
    }

    // Metode logout untuk web (tidak berubah)
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}