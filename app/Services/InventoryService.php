<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Inventory())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->search, function ($query) use ($request) {
                $query->where('sku', 'like', "%$request->search%");
                $query->orWhere('available_stock',$request->search);
            });
            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            $inventory = Inventory::create($data);

            return response()->json([
                'messages' => ['Inventory created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($inventory, array $data): JsonResponse
    {
        try {
            $inventory->update($data);

            return response()->json([
                'messages' => ['Inventory updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($inventory): JsonResponse
    {
        try {
            $inventory->delete();

            return response()->json([
                'messages' => ['Inventory deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getByProductId($request, $productId): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Inventory())->newQuery()
                                        ->whereProductId($productId)
                                        ->orderBy($sortBy, $sortOrder);

            $query->when($request->search, function ($query) use ($request) {
                $query->where('sku', 'like', "%$request->search%");
                $query->orWhere('available_stock',$request->search);
            });
            $results = $query->paginate($perPage, ['*'], 'page', $page);
            return response()->json($results, 200);

        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getLowStock($request): JsonResponse
    {
        try {

            $result = (new Inventory())->newQuery()->where('available_stock','<=',12)->limit(20)->get();

            return response()->json($result, 200);

        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
