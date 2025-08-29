<?php

namespace App\Services;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class SettingServies
{
    public function update(UpdateUserRequest $request)
    {
        try {
            $user = Auth::user();
            $user->update([
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
            ], 500);
        }
    }
}
