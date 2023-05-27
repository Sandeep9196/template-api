<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\OrderProductService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderService
{
    public function __construct(private OrderProductService $orderProductService)
    {
    }
    public function clearOrder(array $order): JsonResponse
    {
        return response($order)->json();
    }

    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 20;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'orders.created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            $query = (new Order())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('orders.created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('orders.created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });
            $query->when($request->status, function ($q) use ($request) {
                if ($request->status == 'confirmed') {
                    $q->whereIn('orders.status', ['confirmed', 'loser']);
                } else
                    $q->where('orders.status', $request->status);
            });
            $query->when($request->order_id, function ($q) use ($request) {
                $q->where('orders.order_id', 'like', '%' . $request->order_id . '%');
            });
            $query->when($request->product_name, function ($q) use ($request) {
                $q->where('translations.translation', 'like', '%' . $request->product_name . '%');
            });
            Session::put("query_order_session", true);
            $results = $query->select('orders.*')->with(['orderProduct', 'orderProduct.product', 'orderProduct.product.translations'])
                ->leftJoin('order_product', 'order_product.order_id', '=', 'orders.id')
                ->leftJoin('products', 'products.id', '=', 'order_product.product_id')
                ->leftJoin('translations', function ($q) {
                    $q->on('products.id', '=', 'translations.translationable_id')
                        ->where('translations.translationable_type', 'App\Models\Product')
                        ->where('translations.field_name', 'name');
                })->groupBy('orders.id')->orderBy($sortBy, $sortOrder)->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function paginateCustomer($request): JsonResponse
    {
        try {

            $perPage = $request->rowsPerPage ?: 20;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $itemsPaginated = Order::where('customer_id', Auth()->user()->id)->with(['orderProducts.product'])
                ->orderBy($sortBy, $sortOrder)->paginate($perPage, ['*'], 'page', $page);

            $itemsTransformed = $itemsPaginated
                ->getCollection()
                ->map(function ($item) {
                    $orderProduct = OrderProduct::with('product')->whereOrderId($item->id)->get();
                    return [
                        'id' => $item->id,
                        'customer_id' => $item->customer_id,
                        'order_id' => $item->order_id,
                        'total_amount' => $orderProduct->sum('amount'),
                        'total_products' => $orderProduct->count(),
                        'total_quantity' => $orderProduct->sum('quantity'),
                        'status' => $item->status,
                        'created_at' => Carbon::parse($item->created_at)->format('d-m-Y H:i:s'),
                        'order_products' => $orderProduct  //with('product')->
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
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function order($order): JsonResponse
    {
        try {

            $result = Order::where('customer_id', Auth()->user()->id)->whereId($order->id)
                ->where('status', 'reserved')->with(['orderProduct', 'orderProduct.product'])->latest('orders.created_at')->first();
            if (empty($result)) {
                return response()->json([
                    'messages' => ['No order to checkout'],
                ], 200);
            } else {
                return response()->json([
                    'order' => $result,
                ], 200);
            }
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function orderApi($slug): JsonResponse
    {

        try {
            $ops =  OrderProduct::where('status', $slug)
                ->select('order_product.*', DB::raw("GROUP_CONCAT(order_product.id) as ids"))
                ->where('customer_id', Auth()->user()->id)
                ->with(['product'])
                ->orderBy('order_product.id', 'desc')
                ->groupBy('product_id')
                ->get();

            if (!$ops && empty($ops)) {
                return response()->json(['messages' => ['Data Not Found'],], 400);
            }
            foreach ($ops as $key => $opData) {
                $orderId =  Order::whereId($opData->order_id)->first()->order_id;
                $ops[$key]->orderId = $orderId;
                $ops[$key]->ids = explode(',', $opData->ids);
            }
            return response()->json([
                'order' => $ops,
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {

            $data["total_amount"] = 0;
            $data["total_products"] = count($data['product_details']);
            $data["total_quantity"] = count($data['product_details']);
            $product = [];
            foreach ($data['product_details'] as $pData) {
                $productData = $pData;
                $productData['product_id'] = $pData['product_id'];
                $productData['amount'] = $pData['amount'];
                array_push($product, $productData);
                $data["total_amount"]   += (int) $pData['amount'];
            }
            $pdataArray['product'] = $product;
            $custmerId = Auth()->user()->id;
            $orderId = Order::create(
                array_merge($data, array('customer_id' => $custmerId, 'order_id' => getRandomIdGenerate('BD'), 'status' => 'reserved'))
            )->id;
            $this->orderProductService->store($data['product_details'], $custmerId, $orderId, ['status' => 'reserved']);
            $result = Order::where('id', $orderId)->with(['orderProduct.product'])->first();
            return response()->json([
                'messages' => ['Order created successfully'],
                'data'     => $result,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function getDashboardCounts(): JsonResponse
    {
        try {
            $result['orderCount']   = Order::where('customer_id', Auth()->user()->id)->where('status', 'confirmed')->count();

            $query =  (new Address())->newQuery();
            $modelData = Auth::user();
            $query->when($modelData, function ($query) use ($modelData) {
                $query->whereAddressableType(Customer::class)
                    ->whereAddressableId($modelData->id);
            });
            $result['addressCount'] = $query->count();

            $result['whishlistCount'] = Favorite::where('customer_id', Auth()->user()->id)->count();

            return response()->json([
                'messages' => ['Dashboard Count Data'],
                'data'     => $result,
            ], 201);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function orderGetById($orderId): JsonResponse
    {
        try {
            Session::put('get_order_data', '');
            $result = OrderProduct::where('order_id', $orderId)->with(['product'])->get();
            $result = $result->makeHidden(['prices']);
            if (!empty($result)) {
                return response()->json([
                    'messages' => ['Order Data fetched successfully'],
                    'data'     => $result->toArray(),
                ], 200);
            } else {
                return response()->json([
                    'messages' => ['No matched data'],
                ], 200);
            }
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function orderGetByIdApi($orderId): JsonResponse
    {
        try {

            $result = Order::where('id', $orderId)
                ->with(['orderProducts', 'orderProducts.product'])
                ->first();
            $result->created_at = date('d-m-Y H:i:s', strtotime($result->created_at));
            return response()->json([
                'messages' => ['Order Data By Order ID'],
                'data'     => $result,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function updateStatus($data, $order): JsonResponse
    {
        try {
            DB::transaction(function () use ($data, $order) {
                $orderProducts = OrderProduct::where('order_id', $order->id)->get();
                $status = $data['status'];
                $update = 1;
                 foreach ($orderProducts as $op) {

                    OrderProduct::whereId($op->id)->update([
                        'status' => $data['status']
                    ]);
                }
                if ($update) {
                    $order->update([
                        'status' => $data['status']
                    ]);
                }
            });

            return response()->json([
                'message' => 'Status update Successfully'
            ], 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function updateOrders($data): JsonResponse
    {
        try {
            $status = $data['status'];
            foreach ($data['orders'] as $o) {
                $order = Order::whereId($o['order_id'])->first();
                $orderProductIds = [];
                if (!empty($o['order_product_id'])) {
                    $orderProductIds = $o['order_product_id'];
                }
                $orderProduct = $order->orderProduct;
                $orderStatus = 'confirmed';
                foreach ($orderProduct as $op) {
                    if (!in_array($op->id, $orderProductIds)) {
                        $orderStatus = 'reserved';
                    } else {
                        $deal = $op->product->deals;
                        OrderProduct::whereId($op->id)->update([
                            'status' => $status
                        ]);
                    }
                }
                Order::whereId($order->id)->update([
                    'status' => $orderStatus
                ]);
            }

            return response()->json([
                'message' => 'Orders update Successfully'
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function cancelOrder(array $data): JsonResponse
    {
        try {
            $orders = OrderProduct::whereIn('id', $data['order_product_id'])->where('customer_id', Auth()->user()->id)->get();
            foreach ($orders as $orderProduct) {
                OrderProduct::where('id', $orderProduct->id)->update(['status' => 'canceled']);
                $order =  Order::whereId($orderProduct->order_id)->first();
                $orderId =  $order->order_id;
                $orderProduct->orderId = $orderId;
                $orderProducts = OrderProduct::where('order_id', $orderProduct->order_id)->count();
                $orderProductCancel = OrderProduct::where('order_id', $orderProduct->order_id)->where(['status' => 'canceled'])->count();
                if ($orderProducts == $orderProductCancel) {
                    Order::whereId($orderProduct->order_id)->update(['status' => 'canceled']);
                } else {
                    Order::whereId($orderProduct->order_id)->update(['status' => 'remaining']);
                }
            }
            return response()->json([
                'messages' => ['Order Canceled Successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
