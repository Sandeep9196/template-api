<?php

namespace App\Services;

use App\Models\AddToCart;
use Illuminate\Http\JsonResponse;

class AddToCartService
{
    public function addToCart(array $data): JsonResponse
    {
        try {
            unset($data['lang_id']);
            foreach ($data as $dataVal) {
                AddToCart::create([
                    'customer_id' => auth()->user()->id,
                    'p_id'        => $dataVal['id'],
                    'p_name'      => $dataVal['pname'],
                    'quantity'    => $dataVal['quantity'],
                    'price'       => $dataVal['price'],
                    'image'       => !empty($dataVal['image']) ?? $dataVal['image'],
                ]);
            }
            return response()->json([
                'status' => true,
                'messages' => ['Your product has been added to cart.'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
