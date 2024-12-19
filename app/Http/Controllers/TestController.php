<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function testNewsapi()
    {
        $this->newsService->testNewsapi();
    }

    public function testNytimes()
    {
        $this->newsService->testNytimes();
    }

    public function testOpennews()
    {
        $this->newsService->testOpennews();
    }
}
