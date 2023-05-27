<?php

namespace App\Services;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FavoriteService
{


    public function list($request, $customer_id): JsonResponse
    {

        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $customer_id = !empty($customer_id) ? $customer_id : auth('sanctum')->user()->id;
            $query = (new Favorite())->newQuery()->whereCustomerId($customer_id);
            $results = $query->select('favorites.*')->with('product','product.image:id,path,fileable_id')->paginate($perPage, ['*'], 'page', $page);
            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function addToFavorites(array $data): JsonResponse
    {
        try {
            $data['customer_id'] = auth('sanctum')->user()->id;
            DB::transaction(function () use ($data) {
                Favorite::create($data);
            });

            return response()->json([
                'messages' => ['Product added to favorite successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function removeFromFavorites(array $data): JsonResponse
    {
        try {
            $data['customer_id'] = auth()->user()->id;
            DB::transaction(function () use ($data) {
                Favorite::where('product_id',$data['product_id'])->where('customer_id',$data['customer_id'])->delete();
            });

            return response()->json([
                'messages' => ['Product Removed from favorite successfully'],
            ], 201);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
}
