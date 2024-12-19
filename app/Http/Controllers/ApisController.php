<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Illuminate\Support\Facades\Log;

class ApisController extends Controller
{
    public function get()
    {
        try {
            return Api::get();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
