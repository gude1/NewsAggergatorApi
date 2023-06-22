<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /** Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyword = request("keyword");
        $date = request("date");
        $category = request('category');
        $page = request("page") ? request("page") : 1;
    }

    /**
     * Search Through a List
     */
    public function search(Request $request)
    {
        try {
            $keyword = request("keyword");
            $source = request("source");
            $date = request("date");
            $category = request('category');
            $page = request("page") ? request("page") : 1;

            $newsapi_results = $this->searchNewsApiOrg($keyword, $date, $category, $page);
            $newyork_results = $this->searchNewYorkTimesNews($keyword, $date, $category, $page);
            $guardian_results = $this->searchGuardianNews($keyword, $date, $category, $page);

            return response()->json([
                "data" => array_merge($newsapi_results, $newyork_results, $guardian_results),
            ], 200);
        } catch (\Throwable $th) {
            Log::error("NewsController.search: {$th->getMessage()}");
            return response()->json([
                "error" => "Request failed could not process your request at the moment please try again"
            ], 500);
        }
    }

    /**
     * Function to search guardian news api
     */
    public function searchGuardianNews($keyword = "", $date = "", $category = "", $page = 1)
    {
        try {
            $params = [];
            if (!$keyword) {
                return [];
            }
            if ($keyword) {
                $params["q"] = $keyword;
            }
            if ($date) {
                $params["from-date"] = $date;
                $params["to-date"] = $date;
            }
            if ($category) {
                $params["section"] = $category;
            }
            if ($page) {
                $params["page"] = $page;
            }

            $req = Http::get(env("GUARDIAN_NEWS_SEARCH_URL"), array_merge($params, [
                "api-key" => env("GUARDIAN_NEWS_API_kEY"),
            ]));
            if ($req->status() == 200) {
                $data = json_decode($req);
                $articles = $data->response->results;
                $result_arr = [];
                for ($i = 0; $i < count($articles); $i++) {
                    array_push($result_arr, [
                        "title" => $articles[$i]->webTitle,
                        "source" => "guardian news",
                        "category" => $articles[$i]->category,
                        "author" => null,
                        "desc" => null,
                        "url" => $articles[$i]->webUrl,
                        "image" => null,
                        "date" => $articles[$i]->webPublicationDate,
                    ]);
                }
                return $result_arr;
            }
            return [];
        } catch (\Throwable $th) {
            Log::error("NewsController.searchGuardianNews: {$th->getMessage()}");
            return [];

        }
    }

    /**
     * Function to search newsapi.org
     */
    public function searchNewsApiOrg($keyword = "", $date = "", $category = "", $page = 1)
    {
        try {
            $params = [];
            if (!$keyword) {
                return [];
            }
            if ($keyword) {
                $params["q"] = $keyword;
            }
            if ($date) {
                $params["from"] = $date;
                $params["to"] = $date;
            }
            if ($category) {
                $params["category"] = $category;
            }
            if ($page) {
                $params["page"] = $page;
            }

            $req = Http::get(env("NEWSAPIORG_SEARCH_URL"), array_merge($params, [
                "apiKey" => env("NEWSAPIORG_API_KEY"),
            ]));

            if ($req->status() == 200) {
                $result = json_decode($req);
                $articles = $result->articles;
                $result_arr = [];
                for ($i = 0; $i < count($articles); $i++) {
                    array_push($result_arr, [
                        "title" => $articles[$i]->title,
                        "source" => "newsapiorg",
                        "category" => null,
                        "author" => $articles[$i]->author,
                        "desc" => $articles[$i]->description,
                        "url" => $articles[$i]->url,
                        "image" => $articles[$i]->urlToImage,
                        "date" => $articles[$i]->publishedAt,
                    ]);
                }
                return $result_arr;
            }
            return [];
        } catch (\Throwable $th) {
            Log::error("NewsController.searchNewsApiOrg: {$th->getMessage()}");
            return [];
        }
    }

    public function searchNewYorkTimesNews($keyword = "", $date = "", $category = "", $page = 1)
    {
        try {
            $params = [];
            if (!$keyword) {
                return [];
            }
            if ($keyword) {
                $params["q"] = $keyword;
            }
            if ($date) {
                $params["begin_date"] = $date;
                $params["end_date"] = $date;
            }
            if ($category) {
                $params["fq"] = "news_desk:$category";
            }
            if ($page) {
                $params["page"] = $page;
            }

            $req = Http::get(env("NEWYORKTIMES_SEARCH_URL"), array_merge($params, [
                "api-key" => env("NEWYORKTIMES_API_KEY"),
            ]));

            if ($req->status() == 200) {
                $result = json_decode($req);
                $articles = $result->response->docs;
                $result_arr = [];
                for ($i = 0; $i < count($articles); $i++) {
                    array_push($result_arr, [
                        "title" => $articles[$i]->headline->main,
                        "source" => $articles[$i]->source,
                        "category" => $articles[$i]->news_desk,
                        "author" => null,
                        "desc" => $articles[$i]->snippet,
                        "url" => $articles[$i]->web_url,
                        "image" => null,
                        "date" => $articles[$i]->pub_date,
                    ]);
                }
                return $result_arr;
            }
            return [];
        } catch (\Throwable $th) {
            Log::error("NewsController.searchNewsApiOrg: {$th->getMessage()}");
            return [];
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}