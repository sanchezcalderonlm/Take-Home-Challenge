<?php

namespace App\Respository;

use App\Enum\ApiType;
use App\Models\Source;
use Illuminate\Pagination\LengthAwarePaginator;

class SourceRepository
{
    public function store(ApiType $apiId, string $source, string $name): Source
    {
        return Source::create([
            'api_id' => $apiId,
            'source' => $source,
            'name' => $name
        ]);
    }

    public function exist(ApiType $apiId, string $source, string $name): Source|null
    {
        return Source::where('api_id', $apiId)
            ->where('source', $source)
            ->where('name', $name)
            ->first();
    }

    public function getFirstAfterId(ApiType $apiId, int $id = 0): Source|null
    {
        return Source::where('api_id', $apiId)
            ->where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();
    }

    public function get(int|null $apiId = null): LengthAwarePaginator
    {
        $query = new Source;
        if (!empty($apiId)) {
            $query = $query->where('api_id', $apiId);
        }
        return $query->paginate();
    }
}
