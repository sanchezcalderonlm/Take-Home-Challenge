<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'api_id' => 'integer|exists:apis,id',
            'source_id' => 'integer|exists:sources,id',
            'author' => 'string',
            'title' => 'string',
            'description' => 'string',
            'content' => 'string',
            'published_at' => 'date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return $this->newsService->getNews($request->all());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
