<?php

namespace App\Services;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\LoginLog;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Transaction;
class StatsService
{

    public function getThisMonthOnlineUsers(): JsonResponse
    {
        try {
            $membersCount = LoginLog::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->get()->count();

            $lastMonthMembersCount = LoginLog::whereBetween('created_at', [
                Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                Carbon::now()->subMonthNoOverflow()->endOfMonth(),
            ])->get()->count();

            $total = $membersCount + $lastMonthMembersCount;
            $cumulativePercent = (($membersCount - $lastMonthMembersCount) / ($total === 0 ? 1 : $total)) * 100;

            return response()->json([
                'current' => $membersCount,
                'last_month' => $lastMonthMembersCount,
                'percentage' => $cumulativePercent,
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }
    public function getThisMonthNewMembers(): JsonResponse
    {
        try {
            $membersCount = Customer::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->get()->count();

            $lastMonthMembersCount = LoginLog::whereBetween('created_at', [
                Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                Carbon::now()->subMonthNoOverflow()->endOfMonth(),
            ])->get()->count();

            $total = $membersCount + $lastMonthMembersCount;
            $cumulativePercent = (($membersCount - $lastMonthMembersCount) / ($total === 0 ? 1 : $total)) * 100;

            return response()->json([
                'current' => $membersCount,
                'last_month' => $lastMonthMembersCount,
                'percentage' => $cumulativePercent,
            ], 200);

            return response()->json($membersCount, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getThisMontTransaction(): JsonResponse
    {
        try {
            $transctionAmount = Transaction::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->sum('amount');

            $lastMonthTransactionAmount = Transaction::whereBetween('created_at', [
                Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                Carbon::now()->subMonthNoOverflow()->endOfMonth(),
            ])->sum('amount');
            $total = $transctionAmount + $lastMonthTransactionAmount;
            $cumulativePercent = (($transctionAmount - $lastMonthTransactionAmount) / ($total == 0 ? 1 : $total )) * 100;

            return response()->json([
                'current' => $transctionAmount,
                'last_month' => $lastMonthTransactionAmount,
                'percentage' => $cumulativePercent,
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getOnlineStats($request): JsonResponse
    {
        try {
            $query = (new LoginLog)->newQuery();
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

            $data = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')->orderBy('date', 'asc')->get();

            return response()->json($data, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getRecentMemberOnline(): JsonResponse
    {
        try {
            $query = (new LoginLog())->newQuery()->where('user_type', Customer::class);

            $query->leftJoin('customers', 'customers.id', '=', 'login_logs.user_id')
                ->select([
                    'login_logs.*',
                    'customers.member_ID',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.idd',
                    'customers.phone_number',
                    'customers.id',
                    'customers.display_name'
                ]);
            $data = $query->orderBy('created_at', 'desc')->groupBy('phone_number')->distinct()->take(10)->get();

            return response()->json($data, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getRecentTransactions(): JsonResponse
    {
        try {
            $query = (new Transaction())->newQuery()->orderBy('created_at', 'desc');
            $query->leftJoin('customers', 'customers.id', '=', 'transactions.member_id')
                ->select([ 'transactions.*',  'customers.member_ID',
                'customers.first_name',
                'customers.last_name',
                'customers.idd',
                'customers.phone_number',
                'customers.id',
                'customers.display_name'
            ]);

            $data = $query->take(10)->get();

            return response()->json($data, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function dashboard(): JsonResponse
    {
        try {
            $totalProducts = Product::count();
            $totalProductsActive =  Product::whereStatus('active')->count();
            $totalCustomers = Customer::count();
            $totalOrders = Order::count();
            $totalTransacton = Transaction::count();
            //dd($totalTransacton);
            $totalInActiveProducts = Product::whereStatus('inactive')->count();

            //transfer
            $totalTransferIn = Transaction::whereStatus('Credit')->count();
            $totalTransferOut = Transaction::whereStatus('Debit')->count();
            $data = [
                'total_products' => $totalProducts,
                'total_products_ative' => $totalProductsActive,
                'total_customers' => $totalCustomers,
                'total_order' => $totalOrders,
                'total_transaction' => $totalTransacton,
                'total_products_by_status' => [
                    'active' => $totalProductsActive,
                    'inactive' => $totalInActiveProducts,
                ],
                'total_deals_by_status' => [
                ],
                'total_transfer' => [
                    'in' => $totalTransferIn,
                    'out' => $totalTransferOut,
                ],
            ];
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function slotPurchases($request): JsonResponse {
        try {

            $query = (new OrderProduct())->newQuery();
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
            $result = $query->sum('slots');
            return response()->json(['total' => $result], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function tranferAmount($request): JsonResponse {
        try {

            $query = (new Transaction())->newQuery();
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
            if($request->type == 'Debit') {
                $result = $query->whereTransactionType(TRANSFER_OUT)->whereIn('status',['Debit', 'Success'])->sum('amount');
            } else {
                $result = $query->whereTransactionType(TRANSFER_IN)->whereIn('status',['Credit', 'Success'])->sum('amount');
            }
            return response()->json(['total' => $result], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

}
