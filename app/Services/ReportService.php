<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new State())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where('state_name', 'like', "%$request->search%");
            });

            $results = $query->select('states.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $data = State::create($data);
            });

            return response()->json([
                'messages' => ['State created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($state, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($state, $data) {
                $state->update($data);
            });

            return response()->json([
                'messages' => ['State updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($state): JsonResponse
    {
        try {
            $state->delete();

            return response()->json([
                'messages' => ['State deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }


    public function dashboard(): JsonResponse
    {
        try {
            $totalProducts = DB::table('products')->count();
            $totalProductsActive = DB::table('products')->whereStatus('active')->count();
            $totalCustomers = Customer::count();
            $totalOrders = Order::count();
            $totalCarriers = Carrier::count();
            $data = [
                'total_products' => $totalProducts,
                'total_products_ative' => $totalProductsActive,
                'total_customers' => $totalCustomers,
                'total_order' => $totalOrders,
                'total_carriers' => $totalCarriers,
            ];
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
