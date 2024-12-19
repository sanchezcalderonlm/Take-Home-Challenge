<?php

namespace App\Http\Controllers;

use App\Respository\SourceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SourceController extends Controller
{
    public SourceRepository $sourceRepository;

    public function __construct(SourceRepository $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'api_id' => 'integer|exists:apis,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return $this->sourceRepository->get(
                $request['api_id'] ?? null,
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
