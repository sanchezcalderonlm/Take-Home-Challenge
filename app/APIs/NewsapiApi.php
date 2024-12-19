<?php

namespace App\APIs;

use App\Enum\ApiType;
use App\Jobs\NewsapiJob;
use App\Respository\NewsRepository;
use App\Respository\SourceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use jcobhams\NewsApi\NewsApi;

class NewsapiApi implements ApiInterface
{
    public NewsApi $newsapi;
    public string|null $beginDate = null;
    public string|null $endDate = null;
    public NewsRepository $newsRepository;
    public SourceRepository $sourceRepository;

    public function __construct(NewsRepository $newsRepository, SourceRepository $sourceRepository)
    {
        $this->newsapi = new NewsApi(config('news_api.news_api_org'));
        $this->newsRepository = $newsRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function setBeginDate(Carbon $dateTime): static
    {
        $this->beginDate = $dateTime->format('Y-m-d');
        return $this;
    }

    public function setEndDate(Carbon $dateTime): static
    {
        $this->endDate = $dateTime->format('Y-m-d');
        return $this;
    }

    public function fetchSourceAndUpdateNews(): void
    {
        $this->fetchSource();
        $source = $this->sourceRepository->getFirstAfterId(ApiType::NEWSAPI);
        if ($source) {
            $this->updateNews($source->id, $source->source);
        }
    }

    public function fetchSource(): void
    {
        $source = $this->newsapi->getSources(null, 'en', 'us');
        $sources = (array)$source->sources;
        $sources = array_map(function ($v) {
            return (object)['source' => $v->id, 'name' => $v->name];
        }, $sources);
        foreach ((object)$sources as $v) {
            $source = $this->sourceRepository->exist(ApiType::NEWSAPI, $v->source, $v->name);
            if (!$source) {
                $this->sourceRepository->store(ApiType::NEWSAPI, $v->source, $v->name);
            }
        }
    }

    public function updateNews(int $sourceId, string $source, int $page = 1): void
    {
        $result = $this->newsapi->getEverything(null,
            $source, null,
            null, $this->beginDate, $this->endDate,
            'en', 'publishedAt',
            10, $page
        );
        if ($result) {
            $limit = $result->totalResults;
            $limit = ceil($limit / 10);
            foreach ($result->articles as $v) {
                $v->published_at = Carbon::parse($v->publishedAt);
                $news = $this->newsRepository->exist(
                    ApiType::NEWSAPI,
                    $sourceId,
                    $v->author,
                    $v->title,
                    $v->description,
                    $v->content,
                    $v->published_at
                );
                if (!$news) {
                    $this->newsRepository->store(
                        ApiType::NEWSAPI,
                        $sourceId,
                        $v->author,
                        $v->title,
                        $v->description,
                        $v->content,
                        $v->published_at
                    );
                }
            }
            if ($page == 1) {
                $chain = [];
                for ($s = 2; $s <= $limit; $s++) {
                    $chain[] = new NewsapiJob(Carbon::now(), $s, $sourceId, $source);
                }
                $source = $this->sourceRepository->getFirstAfterId(ApiType::NEWSAPI, $sourceId);
                if ($source) {
                    $chain[] = new NewsapiJob(Carbon::now(), 1, $source->id, $source->source);
                }
                if (!empty($chain)) {
                    Bus::chain($chain)->dispatch();
                }
            }
        }
    }
}
