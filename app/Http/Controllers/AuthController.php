<?php

 namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class AuthController extends Controller
    {
        public function apiLogin(Request $request)
        {
            // Validasi input
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Cek apakah role user diizinkan
                if (!in_array($user->role, ['user', 'admin'])) {
                    return response()->json(['message' => 'Akses ditolak'], 403);
                }

                // Membuat token untuk API
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Login berhasil',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'users_id' => $user->users_id, // Pastikan ini sesuai dengan nama kolom di database
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role,
                        'class' => $user->class,
                        'major' => $user->major,
                    ]
                ]);
            }

            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        public function apiLogout(Request $request)
        {
            // Menghapus token yang digunakan untuk autentikasi
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout berhasil']);
        }

        public function me(Request $request)
        {
            return response()->json([
                'users_id' => $request->user()->users_id,
                'name' => $request->user()->name,
                'username' => $request->user()->username,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                'class' => $request->user()->class,
                'major' => $request->user()->major,
            ]);
        }

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

        public function logoutWeb(Request $request)
        {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }