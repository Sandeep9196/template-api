<?php

namespace App\Services;

use App\Models\Configure;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentGatewayService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'name';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new PaymentGateway())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%");
            });
            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $itemsPaginated = $query->with(['configurable'])->paginate($perPage, ['*'], 'page', $page);
            $itemsTransformed = $itemsPaginated
                ->getCollection()
                ->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "name" => $item->name,
                        "status" => $item->status,
                        "configures" => json_decode($item->configurable->data)
                    ];
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
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $paymentData = ['name' => $data['name']];
                $paymentId =  PaymentGateway::create($paymentData);
                $configurationData = new Configure([
                    'data' => json_encode($data['configuration'])
                ]);
                $paymentId->configurable()->save($configurationData);
            });
            return response()->json([
                'messages' => ['Payment Gateway created Successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update(array $data, $payment): JsonResponse
    {
        try {
            DB::transaction(function () use ($payment, $data) {
                $payment->name = $data['name'];
                $payment->status = $data['status'];
                $payment->update();
                $configurationData = [
                    'data' => json_encode($data['configuration'])
                ];
                $payment->configurable()->update($configurationData);
            });
            return response()->json([
                'messages' => ['Payment Gateway updated Successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function delete($payment): JsonResponse
    {
        $payment->delete();
        return response()->json([
            'messages' => ['Payment Gateway deleted Successfully'],
        ]);
    }
}
