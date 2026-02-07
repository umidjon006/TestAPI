<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. List (Hamma userlar)
    public function index()
    {
        return User::latest()->paginate(10);
    }

    // 2. Create (Yangi user qo'shish)
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'in:admin,student'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Parolni shifrlash
            'role' => $request->role ?? 'student'
        ]);

        return response()->json($user, 201);
    }

    // 3. Show (Bitta userni ko'rish)
    public function show(User $user)
    {
        return $user;
    }

    // 4. Update (Userni tahrirlash)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'email|unique:users,email,' . $user->id, // O'z emailini o'zgartirmasa xato bermasligi uchun
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'role']);

        // Agar parol o'zgartirilayotgan bo'lsa
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    // 5. Delete (Userni o'chirish)
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}