<?php

namespace App\Http\Controllers;

use App\Enum\Role;
use App\Services\UserService;
use App\Http\Requests\AddUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getPaginatedUsers();
        return view('users.index', compact('users'));
    }

    public function showUser()
    {
        $users = $this->userService->getUsersExcludingAuth();
        return response()->json(['success' => true, 'data' => $users]);
    }

    public function add(AddUserRequest $request)
    {
        return $this->userService->add($request);
    }

    public function getUserStatus(Request $request, $userId)
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
