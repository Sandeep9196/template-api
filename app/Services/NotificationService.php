<?php

namespace App\Services;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use stdClass;

class NotificationService
{
    public function paginate($request): JsonResponse
    {
        
        try {
            $perPage = $request->rowsPerPage ?: 20;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->ascending == 'true' ? 'asc' : 'desc';
            $query = (new Notification())->newQuery()->orderBy($sortBy, $sortOrder);


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

            $itemsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            $itemsTransformed = $itemsPaginated
                ->getCollection()
                ->map(function ($item) {
                    $datas = new stdClass();
                    if (!empty($item->data->data)) {
                        $datas = $item->data->data;
                        $datas->id = $item->id;
                        $datas->read_at = $item->read_at;
                        $datas->message = $item->data->message;
                        $datas->date = Carbon::parse($item->created_at)->format('Y-m-d H:m:s');
                    }

                    return  $datas;
                })->toArray();


            $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsTransformed,
                $itemsPaginated->total(),
                $itemsPaginated->perPage(),
                $itemsPaginated->currentPage(),
                [
                    'path' => \Request::url(),
                    'query' => [
                        'page' => $itemsPaginated->currentPage()
                    ]
                ]
            );



            return response()->json($itemsTransformedAndPaginated, 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
    public function update($data, $request)
    {
        $data->read_at = Carbon::now()->format('Y-m-d H:i:s');
        $data->update();
        return response()->json([
            'messages' => ['Notification updated successfully'],
        ], 200);
    }
}
