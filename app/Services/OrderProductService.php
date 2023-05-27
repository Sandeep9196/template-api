<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDeal;
use App\Models\OrderProduct;
use App\Models\SlotDeal;
use Illuminate\Http\JsonResponse;

class OrderProductService
{

    public function store($data, $custmerId, $orderId): JsonResponse
    {
        try {
            foreach ($data as $dataVal) {
                OrderProduct::create(array_merge($dataVal, array('customer_id' => $custmerId, 'order_id' => $orderId)));
            }
            return response()->json([
                'messages' => ['Order Product created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
