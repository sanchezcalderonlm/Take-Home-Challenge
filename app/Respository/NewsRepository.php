<?php

namespace App\Respository;

use App\Enum\ApiType;
use App\Models\News;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsRepository
{
    public function store(ApiType     $apiId, int $sourceId, null|string $author,
                          null|string $title, null|string $description,
                          null|string $content, \DateTime $publishedAt): News
    {
        return News::create([
            'api_id' => $apiId,
            'source_id' => $sourceId,
            'author' => $author,
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'published_at' => $publishedAt,
        ]);
    }

    public function exist(ApiType     $apiId, int $sourceId, null|string $author,
                          null|string $title, null|string $description,
                          null|string $content, \DateTime $publishedAt): News|null
    {
        return News::where('api_id', $apiId)
            ->where('source_id', $sourceId)
            ->where('author', $author)
            ->where('title', $title)
            ->where('description', $description)
            ->where('content', $content)
            ->where('published_at', $publishedAt)
            ->first();
    }

    public function get(int|null $apiId = null, int|null $sourceId = null, null|string $author = null,
                        null|string  $title = null, null|string $description = null,
                        null|string  $content = null, \DateTime|null $publishedAt = null) : LengthAwarePaginator
    {
        $query = new News;
        if (!empty($apiId)) {
            $query = $query->where('api_id', $apiId);
        }
        if (!empty($sourceId)) {
            $query = $query->where('source_id', $sourceId);
        }
        if (!empty($author)) {
            $query = $query->where('author', 'LIKE', '%' . $author . '%');
        }
        if (!empty($title)) {
            $query = $query->where('title', 'LIKE', '%' . $title . '%');
        }
        if (!empty($description)) {
            $query = $query->where('description', 'LIKE', '%' . $description . '%');
        }
        if (!empty($content)) {
            $query = $query->where('content', 'LIKE', '%' . $content . '%');
        }
        if (!empty($publishedAt)) {
            $query = $query->whereDate('published_at', $publishedAt->format('Y-m-d'));
        }

        return $query->paginate();
    }
}
