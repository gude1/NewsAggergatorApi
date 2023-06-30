<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserPreferenceController extends Controller
{
    /** Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * @OA\Post(
     *     path="/api/preference",
     *     tags={"User Preferences"},
     *     summary="Store user's  preference",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserPreferenceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preference updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preference updated"),
     *             @OA\Property(property="preference", ref="#/components/schemas/UserPreference")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request is empty",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Request is empty")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Could not process the request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Could not process your request at the moment, please try again")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function store(StoreUserPreferenceRequest $req)
    {
        try {
            $req_data = $req->only(["category", "author", "source"]);
            if (count($req_data) < 1) {
                return response()->json([
                    "error" => "Request is empty"
                ], 400);
            }
            if ($req->user()->preference) {
                $prefer_authors = $req->user()->preference->authors ? json_decode($req->user()->preference->authors) : [];
                $prefer_sources = $req->user()->preference->sources ? json_decode($req->user()->preference->sources) : [];
                $prefer_categories = $req->user()->preference->categories ? json_decode($req->user()->preference->categories) : [];
            } else {
                $prefer_authors = $prefer_categories = $prefer_sources = [];
            }

            if ($req->category && !in_array($req->category, $prefer_categories)) {
                array_push($prefer_categories, $req->category);
            }
            if ($req->author && !in_array($req->author, $prefer_categories)) {
                array_push($prefer_authors, $req->author);
            }
            if ($req->source && !in_array($req->source, $prefer_sources)) {
                array_push($prefer_sources, $req->source);
            }

            $update = UserPreference::updateOrCreate([
                "user_id" => $req->user()->id
            ], [
                "authors" => json_encode($prefer_authors),
                "sources" => json_encode($prefer_sources),
                "categories" => json_encode($prefer_categories)
            ]);

            if (!$update) {
                return response()->json([
                    "error" => "preference updated",
                ], 400);
            }
            return response()->json([
                "message" => "preference updated",
                "preference" => $update
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserPreferenceController.store: {$th->getMessage()}");
            return response()->json([
                "error" => "Could not process your request at the moment please try again"
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/preference",
     *     tags={"User Preferences"},
     *     summary="Display specified user's preference",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="preference", ref="#/components/schemas/UserPreference")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Could not process the request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Request failed, could not process your request at the moment, please try again")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Request $request)
    {
        try {
            return response()->json([
                "preference" => $request->user()->preference,
            ], 200);
        } catch (\Throwable $th) {
            Log::error("UserController.show: {$th->getMessage()}");
            return response()->json([
                "error" => "Request failed could not process your request at the moment please try again"
            ], 500);
        }
    }
}