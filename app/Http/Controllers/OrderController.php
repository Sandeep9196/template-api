<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Models\Order;
use App\Services\OrderDealService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService, private OrderDealService $orderDealService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->orderService->paginate($request);
    }
    public function paginateCustomer(Request $request)
    {
        return $this->orderService->paginateCustomer($request);
    }

    public function order(Order $slug)
    {
        return $this->orderService->order($slug);
    }
    public function orderApi($slug)
    {
        return $this->orderService->orderApi($slug);
    }

    public function updateStatus(Request $request, Order $order)
    {
        return $this->orderService->updateStatus($request->all(), $order);
    }

    public function updateOrders(Request $request)
    {
        return $this->orderService->updateOrders($request->all());
    }

    public function getDashboardCounts()
    {
        return $this->orderService->getDashboardCounts();
    }

    public function orderGetById($orderId)
    {
        return $this->orderService->orderGetById($orderId);
    }
    public function orderGetByIdApi($orderId)
    {
        return $this->orderService->orderGetByIdApi($orderId);
    }

    public function all()
    {
        return response()->json(Order::all(), 200);
    }

    public function store(OrderFormRequest $request)
    {
        return $this->orderService->store($request->all());
    }
    public function purchaseHistory(Request $request)
    {
        return $this->orderDealService->paginate($request);
    }
    public function cancelOrder(OrderFormRequest $request)
    {
        return $this->orderService->cancelOrder($request->all());
    }
    public function orderCompleted(Request $request)
    {
        return $this->orderService->orderCompleted($request->all());
    }
}
