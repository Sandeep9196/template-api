<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $orderData)
    {
        $this->orderData = $orderData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->orderData;
        $orders  = Order::whereCustomerId(auth()->user()->id)->whereIn('status', ['reserved', 'remaining'])->get();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $opWhere = ['order_id' => $order->id];
                $orderProducts = OrderProduct::where($opWhere)->whereIn('status', ['reserved', 'remaining'])->get();
                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $op) {
                        OrderProduct::where(['id' => $op->id])->update(['status' => 'canceled']);
                        $orderProduct = OrderProduct::where('order_id', $op->order_id)->count();
                        $orderProductCancel = OrderProduct::where('order_id', $op->order_id)->where(['status' => 'canceled'])->count();
                        $status = ['status' => 'remaining'];
                        if ($orderProduct == $orderProductCancel) {
                            $status = ['status' => 'canceled'];
                        }
                        Order::whereId($op->order_id)->update($status);
                    }
                }
            }
        }
    }
}
