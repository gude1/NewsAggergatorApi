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
use OpenApi\Annotations as OA;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
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
     * @OA\Post(
     *     path="/api/auth/signup",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Signup Success!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="your_token_here")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Could not process your request at the moment, please try again")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Signup request failed, could not process your request at the moment, please try again")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/user",
     *     operationId="getUser",
     *     tags={"User"},
     *     summary="Get user details",
     *     description="Retrieves the user details along with their preferences.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/UserWithPreference")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Request failed could not process your request at the moment please try again")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    /**
     * @OA\Schema(
     *     schema="UserWithPreference",
     *     @OA\Property(property="user", ref="#/components/schemas/User"),
     *     @OA\Property(property="preference", ref="#/components/schemas/Preference")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="User",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="john.doe@example.com")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="Preference",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="category", type="string", example="Technology"),
     *     @OA\Property(property="language", type="string", example="English")
     * )
     */

    public function show(Request $request)
    {
        try {
            return response()->json([
                "user" => $request->user()->load("preference"),
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.show: {$th->getMessage()}");
            return response()->json([
                "error" => "Request failed could not process your request at the moment please try again"
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login Success!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="your_token_here")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to login, please try again")
     *         )
     *     )
     * )
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
                "error" => 'Failed to login, please try again'
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to logout, please try again")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "message" => "Logout successful"
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.logout: {$th->getMessage()}");
            return response()->json([
                "error" => 'Failed to logout, please try again'
            ], 500);
        }
    }

}