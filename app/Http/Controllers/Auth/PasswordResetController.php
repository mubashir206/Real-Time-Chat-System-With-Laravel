<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    public function sendResetLink(SendResetLinkRequest $request)
    {
        return $this->passwordResetService->sendResetLink($request);
    }

    public function showResetForm($token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->passwordResetService->resetPassword($request);
    }
}
