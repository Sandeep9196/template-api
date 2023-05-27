<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new Currency())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%");
            });

            $results = $query->select('currencies.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $currency = Currency::create($data);
            });

            return response()->json([
                'messages' => ['Currency created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($currency, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($currency, $data) {
                $currency->update($data);
            });

            return response()->json([
                'messages' => ['Currency updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($currency): JsonResponse
    {
        try {
            $currency->delete();

            return response()->json([
                'messages' => ['Currency deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
