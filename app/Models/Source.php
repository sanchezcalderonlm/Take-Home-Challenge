<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Source extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_id',
        'source',
        'name',
    ];

    public function api(): HasOne
    {
        return $this->hasOne(Api::class, 'id', 'api_id');
    }
}
