<?php

namespace App\APIs;

use App\Enum\ApiType;
use App\Respository\NewsRepository;
use App\Respository\SourceRepository;
use Carbon\Carbon;
use SimpleXMLElement;

class OpennewsRSS
{
    public NewsRepository $newsRepository;
    public SourceRepository $sourceRepository;
    public SimpleXMLElement $rss;

    public function __construct(NewsRepository $newsRepository, SourceRepository $sourceRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->sourceRepository = $sourceRepository;
        $this->rss = simplexml_load_file('https://source.opennews.org/rss/');
    }

    public function findInHtml($html, $init, $end, $include, $function)
    {
        $initCount = strlen($init);
        $endCount = strlen($end);
        $res = [];
        $match = 0;
        for ($i = 0; $i < strlen($html); $i++) {
            if (substr($html, $i, $initCount) == $init) {
                $res[$match] = substr($html, ($i + $initCount), strlen($html));
                $match++;
            }
        }
        for ($i = 0; $i < $match; $i++) {
            for ($si = 0; $si < strlen($res[$i]); $si++) {
                if (substr($res[$i], $si, $endCount) == $end) {
                    $res[$i] = substr($res[$i], 0, $si);
                    if ($include == "on") {
                        $res[$i] = $init . '' . $res[$i] . '' . $end;
                    }
                    break;
                }
            }
        }
        return $function($res, $match);
    }

    public function skipHtml(string $text)
    {
        $labels = $this->findInHtml($text, '<', '>', 'on', function ($arr, $len) {
            return $arr;
        });
        foreach ($labels as $label) {
            $regex = '/' . preg_quote($label, '/') . '/';
            $text = preg_replace($regex, "", $text, 1);
        }
        return preg_replace("/\r|\n/", ' ', $text);
    }

    public function createSlug(string $string): string
    {
        return strtolower(str_replace(' ', '-', $string));
    }

    public function fetchSourceAndUpdateNews(): void
    {
        $sourceName = 'Open News';
        $source = $this->sourceRepository->exist(ApiType::OPENNEWS, $this->createSlug($sourceName), $sourceName);
        if (!$source) {
            $source = $this->sourceRepository->store(ApiType::OPENNEWS, $this->createSlug($sourceName), $sourceName);
        }
        foreach ($this->rss->channel->item as $v) {
            $publishedAt = Carbon::createFromFormat('D, d M Y H:i:s O', $v->pubDate);
            $description = $this->skipHtml($v->description);
            $news = $this->newsRepository->exist(
                ApiType::OPENNEWS,
                $source->id,
                null,
                $v->title,
                $description,
                $description,
                $publishedAt
            );
            if (!$news) {
                $this->newsRepository->store(
                    ApiType::OPENNEWS,
                    $source->id,
                    null,
                    $v->title,
                    $description,
                    $description,
                    $publishedAt
                );
            }
        }
    }
}
