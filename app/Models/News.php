<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class News extends Model
{
    protected $fillable = [
        'api_id',
        'source_id',
        'author',
        'title',
        'description',
        'content',
        'published_at',
    ];

    public function api(): HasOne
    {
        return $this->hasOne(Api::class, 'id', 'api_id');
    }

    public function source(): HasOne
    {
        return $this->hasOne(Source::class, 'id', 'source_id');
    }
}
