<?php

namespace App\Services;

use App\APIs\NewsapiApi;
use App\APIs\NytimesApi;
use App\APIs\OpennewsRSS;
use App\Respository\NewsRepository;
use App\Respository\SourceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsService
{
    public NewsapiApi $newsapiApi;
    public NytimesApi $nytimesApi;
    public OpennewsRSS $opennewsRSS;
    public NewsRepository $newsRepository;
    public SourceRepository $sourceRepository;

    public function __construct(NewsapiApi     $newsapiApi, NytimesApi $nytimesApi, OpennewsRSS $opennewsRSS,
                                NewsRepository $newsRepository, SourceRepository $sourceRepository)
    {
        $this->newsapiApi = $newsapiApi;
        $this->nytimesApi = $nytimesApi;
        $this->opennewsRSS = $opennewsRSS;
        $this->newsRepository = $newsRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function testNewsapi(): void
    {
        try {
            $endDate = $today = Carbon::now();
            $beginDate = $today->clone()->subDay();
            $this->newsapiApi->setBeginDate($beginDate)
                ->setEndDate($endDate)
                ->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function testNytimes(): void
    {
        try {
            $endDate = $today = Carbon::now();
            $beginDate = $today->clone()->subDay();
            $this->nytimesApi->setBeginDate($beginDate)
                ->setEndDate($endDate)
                ->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function testOpennews(): void
    {
        try {
            $this->opennewsRSS->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function getNews($request)
    {
        return $this->newsRepository->get(
            $request['api_id'] ?? null,
            $request['source_id'] ?? null,
            $request['author'] ?? null,
            $request['title'] ?? null,
            $request['description'] ?? null,
            $request['content'] ?? null,
            !empty($publishedAt) ? Carbon::createFromFormat('Y-m-d', $publishedAt) : null
        );
    }

}
