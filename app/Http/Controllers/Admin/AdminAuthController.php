<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        /** @var \App\Models\User */
        $admin = Admin::where(['email' => $request->email])->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $token = $admin->createToken($request->server('HTTP_USER_AGENT') ?? 'default_device')->plainTextToken;

        return response([
            'success' => true,
            'data'    => [
                'token' => $token,
                'admin' => $admin
            ]
        ],200);
    }

    public function getMe()
    {
        $admin = Admin::find(auth('admins')->user()->id);

        return response([
            'success' => true,
            'data'    => $admin
        ],200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'success' => true,
            'message' => 'This user has been logged out',
        ], 200);
    }
}
