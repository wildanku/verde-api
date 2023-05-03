<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        /** @var \App\Models\User */
        $user = User::where(['email' => $request->email])->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $token = $user->createToken($request->server('HTTP_USER_AGENT') ?? 'default_device')->plainTextToken;

        return response([
            'success' => true,
            'data'    => [
                'token' => $token,
                'user'  => new UserResource($user)
            ]
        ],200);
    }

    public function register(UserRegistrationRequest $request)
    {
        $user = DB::transaction(function() use($request) {
            return User::create($request->except(['password','password_confirmation']) + [
                'password' => Hash::make($request->password)
            ]);
        });

        $token = $user->createToken($request->server('HTTP_USER_AGENT') ?? 'default_device')->plainTextToken;

        return response([
            'success' => true,
            'data'    => [
                'token' => $token,
                'user'  => new UserResource($user)
            ]
        ],200);

    }

    public function getMe()
    {
        $user = User::find(auth('users')->user()->id);

        return response([
            'success' => true,
            'data'    => new UserResource($user)
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
