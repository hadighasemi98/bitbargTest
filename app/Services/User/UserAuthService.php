<?php

namespace App\Services\User;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @test Tests\Feature\AuthControllerTest
 */
class UserAuthService
{
    public function register(SignUpRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('LaravelPassport')->accessToken;

        return response()->json(data: [
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], status: 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (! Auth::guard('web')->attempt(credentials: $credentials)) {
            return response()->json(data: ['success' => false, 'message' => 'Unauthorized'], status: 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken(name: 'user')->accessToken;

        return response()->json(data: [
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], status: 200);

    }
}
