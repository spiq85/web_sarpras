<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'class' => 'required|in:X,XI,XII',
            'major' => 'required|in:RPL,TJKT,PSPT,ANIMASI,TE',

            
        ]);

        $user = User::create([
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
        return response()->json(User::findOrFail($id));
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
            'username' => 'sometimes|required|string|unique:users,username,' . $id . ',users_id',
            'email' => 'sometimes|required|email|unique:users,email,' . $id . ',users_id',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,user',
            'class' => 'sometimes|required|in:X,XI,XII',
            'major' => 'sometimes|required|in:RPL,TJKT,PSPT,ANIMASI,TE',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
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
        User::destroy($id);
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
