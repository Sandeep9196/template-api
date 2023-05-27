<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderDealService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 20;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $results = Order::where('customer_id', Auth()->user()->id)->with(['orderDeals', 'products'])
                ->orderBy($sortBy, $sortOrder)->paginate($perPage, ['*'], 'page', $page);
            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
