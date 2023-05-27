<?php

namespace App\Services;

use App\Models\Settlement;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SettlementService
{

    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new Settlement())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->actual_cost, function ($query) use ($request) {
                $query->where('actual_cost', $request->actual_cost);
            });
            $query->when($request->received_amount, function ($query) use ($request) {
                $query->where('received_amount', $request->received_amount);
            });
            $query->when($request->result_type, function ($query) use ($request) {
                $query->where('result_type', $request->result_type);
            });
            $query->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            });
            $query->when($request->profit_loss_amount, function ($query) use ($request) {
                $query->where('profit_loss_amount', $request->profit_loss_amount);
            });

            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getTotalSettlement($request): JsonResponse
    {

        try {
            $query = (new Settlement())->newQuery();
            $query->when($request->dates, function ($query) use ($request) {


                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });
            $results = $query->selectRaw("SUM(CASE WHEN result_type = 'Profit' THEN profit_loss_amount ELSE 0 END) AS profit_amount, " .
                "SUM(CASE WHEN result_type = 'Loss' THEN profit_loss_amount ELSE 0 END) AS loss_amount")->first();

            $results->profit_amount =  (int)isset($results->profit_amount) && !is_null($results->profit_amount) ? $results->profit_amount : 0;
            $results->loss_amount =  (int)isset($results->loss_amount) ? $results->loss_amount : 0;
            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
