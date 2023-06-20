<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->except(["store", "login"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password, [
                    'rounds' => 15,
                ])
            ]);
            if (!$user) {
                return response()->json([
                    "error" => "Could not process your request at the moment please try agains"
                ], 400);
            }

            return response()->json([
                "message" => "Signup Success!",
                "data" => [
                    "token" => $user->createToken($user->password)->plainTextToken,
                ]
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.store: {$th->getMessage()}");
            return response()->json([
                "error" => "Signup request failed could not process your request at the moment please try again"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            return response()->json([
                "user" => $request->user()->refresh(),
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.show: {$th->getMessage()}");
            return response()->json([
                "error" => "Request failed could not process your request at the moment please try again"
            ], 500);
        }
    }

    /**
     * Log current user session out
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $retrieved_user = User::firstWhere([
                "email" => $request->email,
            ]);

            if (!$retrieved_user) {
                return response()->json([
                    'error' => "User not found"
                ], 400);
            }
            if (!Hash::check($request->password, $retrieved_user->password)) {
                return response()->json([
                    'error' => "Invalid email or password"
                ], 400);
            }

            return response()->json([
                "message" => "Login Success!",
                "data" => [
                    "token" => $retrieved_user->createToken($retrieved_user->password)->plainTextToken,
                ]
            ], 200);

        } catch (\Throwable $th) {
            Log::error("UserController.login: {$th->getMessage()}");
            return response()->json([
                "error" => 'Failed to in please try again'
            ], 500);
        }
    }


    /**
     * Log current user session out
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "message" => "Log out successful"
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.logout: {$th->getMessage()}");
            return response()->json([
                "error" => 'Failed to logout please try again'
            ], 500);
        }
    }
}