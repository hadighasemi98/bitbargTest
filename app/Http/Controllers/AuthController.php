<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Services\User\UserAuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private UserAuthService $userAuthService) {}

    public function register(SignUpRequest $request): JsonResponse
    {
        return $this->userAuthService->register($request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->userAuthService->login($request);
    }
}
