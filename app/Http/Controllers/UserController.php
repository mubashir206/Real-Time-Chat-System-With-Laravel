<?php

namespace App\Http\Controllers;

use App\Enum\Role;
use App\Services\UserService;
use App\Http\Requests\AddUserRequest;

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
}
