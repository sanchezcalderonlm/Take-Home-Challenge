<?php

namespace App\APIs;

use App\Enum\ApiType;
use App\Jobs\NytimesJob;
use App\Respository\NewsRepository;
use App\Respository\SourceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class NytimesApi implements ApiInterface
{

    public string $url;
    public string|null $beginDate = null;
    public string|null $endDate = null;
    public string $apiKey;
    public NewsRepository $newsRepository;
    public SourceRepository $sourceRepository;

    public function __construct(NewsRepository $newsRepository, SourceRepository $sourceRepository)
    {
        $this->url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
        $this->apiKey = config('news_api.ny_times_key');
        $this->newsRepository = $newsRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function setBeginDate(Carbon $dateTime): static
    {
        $this->beginDate = $dateTime->format('Ymd');
        return $this;
    }

    public function setEndDate(Carbon $dateTime): static
    {
        $this->endDate = $dateTime->format('Ymd');
        return $this;
    }

    public function createSlug(string $string): string
    {
        return strtolower(str_replace(' ', '-', $string));
    }

    public function fetchSourceAndUpdateNews(int $page = 0): void
    {
        $request = Http::get($this->url .
            '?begin_date=' . $this->beginDate .
            '&end_date=' . $this->endDate .
            '&api-key=' . $this->apiKey .
            '&page=' . $page);

        if ($request->getStatusCode() === 200) {
            $body = json_decode($request->body());
            $limit = $body->response->meta->hits;
            $limit = ceil($limit / 10) - 1;
            $sources = array_map(function ($v) {
                return (object)[
                    'source' => $this->createSlug($v->source),
                    'name' => $v->source,
                    'author' => $v->byline?->original,
                    'title' => $v->abstract,
                    'description' => $v->snippet,
                    'content' => $v->lead_paragraph,
                    'published_at' => $v->pub_date,
                ];
            }, $body->response?->docs);
            foreach ((object)$sources as $v) {
                $source = $this->sourceRepository->exist(ApiType::NYTIMES, $v->source, $v->name);
                if (!$source) {
                    $source = $this->sourceRepository->store(ApiType::NYTIMES, $v->source, $v->name);
                }
                $v->published_at = Carbon::parse($v->published_at);
                $news = $this->newsRepository->exist(
                    ApiType::NYTIMES,
                    $source->id,
                    $v->author,
                    $v->title,
                    $v->description,
                    $v->content,
                    $v->published_at
                );
                if (!$news) {
                    $this->newsRepository->store(
                        ApiType::NYTIMES,
                        $source->id,
                        $v->author,
                        $v->title,
                        $v->description,
                        $v->content,
                        $v->published_at
                    );
                }
            }
            if ($page == 0) {
                $chain = [];
                for ($s = 1; $s <= $limit; $s++) {
                    $chain[] = new NytimesJob(Carbon::now(), $s);
                }
                if (!empty($chain)) {
                    Bus::chain($chain)->dispatch();
                }
            }
        }
    }
}
