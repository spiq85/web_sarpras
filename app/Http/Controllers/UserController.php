<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Tambahkan validasi untuk 'name'
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'class' => 'required|in:X,XI,XII',
            'major' => 'required|in:RPL,TJKT,PSPT,ANIMASI,TE',
        ]);

        $user = User::create([
            'name' => $request->name, // Pastikan disimpan
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'class' => $request->class,
            'major' => $request->major,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true, // Tambahkan 'success'
                'message' => 'Data user berhasil diambil.', // Tambahkan pesan
                'data' => [ // Bungkus data user di dalam kunci 'data'
                    'users_id' => $user->users_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'class' => $user->class,
                    'major' => $user->major,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching user by ID:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255', // Tambahkan validasi 'name'
            'username' => 'sometimes|required|string|unique:users,username,' . $id . ',users_id',
            'email' => 'sometimes|required|email|unique:users,email,' . $id . ',users_id',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,user',
            'class' => 'sometimes|required|in:X,XI,XII',
            'major' => 'sometimes|required|in:RPL,TJKT,PSPT,ANIMASI,TE',
        ]);

        $data = [
            'name' => $request->name, // Pastikan 'name' diupdate
            'username' => $request->username,
            'email' => $request->email,
            'class' => $request->class,
            'role' => $request->role,
            'major' => $request->major,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        try {
            User::destroy($id);
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting user:', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus user!');
        }
    }
}