<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class NotFoundService
{
    public function index($request): JsonResponse
    {
        $results['error'] = "Invalid URL";
        return response()->json($results, 404);
    }
}
