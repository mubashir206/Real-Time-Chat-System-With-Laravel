<?php

namespace App\Services;

use App\Enum\Role;
use App\Http\Requests\AddUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function getPaginatedUsers()
    {
        $authUser = Auth::user();
        if ($authUser->role == Role::ADMIN) {
            return User::latest()->paginate(10);
        }
        return User::where('id', $authUser->id)->latest()->paginate(10);
    }

    public function getUsersExcludingAuth()
    {
        return User::where('id', '!=', Auth::id())
            ->select('id', 'name')
            ->get();
    }

    public function add(AddUserRequest $request)
    {
        try {
            $user = User::create([
                ...$request->validated(),
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User added successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add user...'
            ], 500);
        }
    }

    public function getUserStatus($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'status' => $user->is_online ? 'online' : 'offline',
            ],
        ]);
    }
}
