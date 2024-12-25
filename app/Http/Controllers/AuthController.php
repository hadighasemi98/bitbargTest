<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
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

        if (Auth::attempt(credentials: ['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('LaravelPassport')->accessToken;

            return response()->json(data: [
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], status: 200);
        }

        return response()->json(data: ['success' => false, 'message' => 'Unauthorized'], status: 401);

    }
}
