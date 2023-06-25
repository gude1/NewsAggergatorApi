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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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