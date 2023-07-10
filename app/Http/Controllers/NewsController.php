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
     * @OA\Get(
     *     path="/api/news",
     *     operationId="getNews",
     *     tags={"News"},
     *     summary="Get a listing of news articles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="page",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="Article title"
     *                     ),
     *                     @OA\Property(
     *                         property="source",
     *                         type="string",
     *                         example="News source"
     *                     ),
     *                     @OA\Property(
     *                         property="category",
     *                         type="string",
     *                         example="Article category"
     *                     ),
     *                     @OA\Property(
     *                         property="author",
     *                         type="string",
     *                         example="Article Author"
     *                     ),
     *                     @OA\Property(
     *                         property="desc",
     *                         type="string",
     *                         example="Article description"
     *                     ),
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         example="https://example.com/article"
     *                     ),
     *                     @OA\Property(
     *                         property="image",
     *                         type="string",
     *                         example="https://example.com/article/image.jpg"
     *                     ),
     *                     @OA\Property(
     *                         property="date",
     *                         type="string",
     *                         example="2023-06-27"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Request failed, could not process your request at the moment. Please try again."
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $preference = request()->user()->preference;
            $categories = $authors = $sources = [];
            if (!is_null($preference)) {
                $categories = $preference->categories ? json_decode($preference->categories) : [];
                $authors = $preference->authors ? json_decode($preference->authors) : [];
                $sources = $preference->sources ? json_decode($preference->sources) : [];
            }
            $page = request("page") ? request("page") : 1;
            $newsapi_params = $guardian_params = $newyork_params = [
                "page" => $page,
            ];
            $newsapi_res = $newyorktimes_res = $guardian_news_res = [];

            if (count($categories) > 0) {
                $newsapi_params["category"] = $categories[0];
                $guardian_params["section"] = $categories[0];
                $newyork_params["fq"] = "section_name:{$categories[0]}";
            }
            if ($authors) {
                $newyork_params["fq"] .= " OR byline:'{$authors[0]}'";
            }

            if (count($sources) < 1) {
                $newsapi_res = $this->getNewsApi($newsapi_params);
                $newyorktimes_res = $this->getNewYorkTimesNews($newyork_params);
                $guardian_news_res = $this->getGuardianNews($guardian_params);
            } else {
                for ($i = 0; $i < count($sources); $i++) {
                    if ($sources[$i] == "newsapi") {
                        $newsapi_res = $this->getNewsApi($newsapi_params);
                    } elseif ($sources[$i] == 'guardian') {
                        $guardian_news_res = $this->getGuardianNews($guardian_params);
                    } elseif ($sources[$i] == "newyorktimes") {
                        $newyorktimes_res = $this->getNewYorkTimesNews($newyork_params);
                    }
                }
            }

            return response()->json([
                "page" => $page,
                "data" => array_merge($newsapi_res, $newyorktimes_res, $guardian_news_res)
            ]);

        } catch (\Throwable $th) {
            Log::error("NewsController.index: {$th->getMessage()}");
            return response()->json([
                "error" => "Request failed could not process your request at the moment please try again"
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/news/search",
     *     operationId="searchNews",
     *     tags={"News"},
     *     summary="Search for news articles",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Source to search",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date to filter by",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category to filter by",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="Article title"
     *                     ),
     *                     @OA\Property(
     *                         property="source",
     *                         type="string",
     *                         example="News source"
     *                     ),
     *                     @OA\Property(
     *                         property="category",
     *                         type="string",
     *                         example="Article category"
     *                     ),
     *                     @OA\Property(
     *                         property="desc",
     *                         type="string",
     *                         example="Article description"
     *                     ),
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         example="https://example.com/article"
     *                     ),
     *                     @OA\Property(
     *                         property="image",
     *                         type="string",
     *                         example="https://example.com/article/image.jpg"
     *                     ),
     *                     @OA\Property(
     *                         property="date",
     *                         type="string",
     *                         example="2023-06-27"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Request failed, could not process your request at the moment. Please try again."
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        try {
            if (!request("keyword")) {
                return response()->json([
                    "error" => "Missing required parameter 'keyword'"
                ], 400);
            }
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
            return $this->getGuardianNews($params);
        } catch (\Throwable $th) {
            Log::error("NewsController.searchGuardianNews: {$th->getMessage()}");
            return [];

        }
    }

    /**
     * Function to return guardian news
     */
    public function getGuardianNews($params = [])
    {
        try {
            $req = Http::get(env("GUARDIAN_NEWS_SEARCH_URL"), array_merge($params, [
                "api-key" => env("GUARDIAN_NEWS_API_kEY"),
                "show-tags" => "contributor",
                "show-element" => "image",
            ]));
            if ($req->status() == 200) {
                $data = json_decode($req);
                $articles = $data->response->results;
                $result_arr = [];
                for ($i = 0; $i < count($articles); $i++) {
                    array_push($result_arr, [
                        "title" => $articles[$i]->webTitle,
                        "source" => "Guardian News",
                        "category" => $articles[$i]->sectionName,
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
            Log::error("NewsController.getGuardianNews: {$th->getMessage()}");
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

            return $this->getNewsApi();
        } catch (\Throwable $th) {
            Log::error("NewsController.searchNewsApiOrg: {$th->getMessage()}");
            return [];
        }
    }

    /**
     * Function to return guardian news
     */
    public function getNewsApi($params = [])
    {
        try {
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
                        "source" => "News Api Org",
                        "category" => null,
                        "author" => $articles[$i]->author || null,
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
            Log::error("NewsController.getNewsApi: {$th->getMessage()}");
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

            return $this->getNewYorkTimesNews($params);
        } catch (\Throwable $th) {
            Log::error("NewsController.searchNewYorkTimesNews: {$th->getMessage()}");
            return [];
        }
    }

    public function getNewYorkTimesNews($params)
    {
        try {
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
                        "author" => count($articles[$i]->byline->person) > 0 ? "{$articles[$i]->byline->person[0]->firstname} {$articles[$i]->byline->person[0]->lastname}" : null,
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
            Log::error("NewsController.searchNewYorkTimesNews: {$th->getMessage()}");
            return [];
        }
    }
}