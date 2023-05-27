<?php

namespace App\Services;

use App\Jobs\CustomerNotification as JobsCustomerNotification;
use App\Models\Bot;
use App\Models\Configure;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Order;
use App\Models\OrderDeal;
use App\Models\OrderProduct;
use App\Models\PriceClaim;
use App\Models\Product;
use App\Models\Settlement;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Slot;
use App\Models\SlotDeal;
use App\Models\TimeInterval;
use App\Models\WinnerDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerNotification;
use Illuminate\Support\Facades\DB;

class WinningService
{

    public function createWinner(array $data): JsonResponse
    {
        try {
            $customerIds = [];
            $where = ['slot_deals.deal_id' => $data['deal_id'], 'slot_deals.status' => 'confirmed'];
            $customerIds = isset($data['customer_ids']) ? $data['customer_ids'] : [];
            if (!empty($data['winner_option'])) {
                if ($data['winner_option'] == '1') {
                    $where += ['slot_deals.is_bot' => '0'];
                } elseif ($data['winner_option'] == '2') {
                    $where += ['slot_deals.is_bot' => '1'];
                }
            }

            $select = [
                'slot_deals.id',
                'slot_deals.order_id',
                'slot_deals.slot_id',
                'slot_deals.deal_id',
                'slot_deals.booking_id',
            ];
            $result = SlotDeal::select($select)->with(['order', 'deal'])
                ->leftJoin('orders', 'orders.id', '=', 'slot_deals.order_id')
                ->inRandomOrder()
                ->where($where)
                ->select('slot_deals.*');

            if (!empty($customerIds)) {
                $result->whereIn('orders.customer_id', $customerIds);
            }
            $result = $result->first();



            if (!empty($result)) {
                /* Checking already winner assigned  */
                $winner = WinnerDetail::with(['customer', 'order', 'product'])->where(['deal_id' => $data['deal_id']])->first();

                if (empty($winner)) {
                    $winnerDetails = [
                        "customer_id" => $result->order->customer_id,
                        "booking_id" => $result->booking_id,
                        "order_id" => $result->order->id,
                        "deal_id" => $result->deal_id,
                        "slot_id" => $result->slot_id
                    ];
                    $deals = Deal::where(['id' => $data['deal_id']])->first();
                    $winnerDetails['product_id'] = $deals->product_id;
                    WinnerDetail::create($winnerDetails);
                    PriceClaim::create($winnerDetails);
                    Deal::where(['id' => $data['deal_id']])->update(['status' => 'settled']);
                    Slot::where(['id' => $result->slot_id])->update(['status' => 'settled']);

                    $settlementData = [
                        'deal_id' => !empty($data['deal_id']) ? $data['deal_id'] : '',
                        'actual_cost' => !empty($data['actual_cost']) ? $data['actual_cost'] : '',
                        'received_amount' => !empty($data['received_amount']) ? $data['received_amount'] : '',
                        'result_type' => !empty($data['result_type']) ? $data['result_type'] : '',
                        'type' => !empty($data['type']) ? $data['type'] : 'deal',
                        'profit_loss_amount' => !empty($data['profit_loss_amount']) ? $data['profit_loss_amount'] : '',
                        'customer_id' => $result->order->customer_id,
                    ];
                    Settlement::create($settlementData);

                    /*  Update winner  */

                    $winnerUpdate = ['status' => 'winner'];
                    if (OrderProduct::where('order_id', $result->order->id)->where('deal_id',  $data['deal_id'])->count() == 1)
                        Order::where('id', $result->order->id)->update($winnerUpdate);
                    OrderProduct::where('order_id', $result->order->id)->where('deal_id',  $data['deal_id'])->update($winnerUpdate);
                    SlotDeal::where('id', $result->id)->update($winnerUpdate);

                    /*  Update winner ends  */

                    /*   Updating order status after winner selection  */

                    $SlotDeal = SlotDeal::select('order_id')
                        ->where('order_id', '!=', $result->order->id)
                        ->where('deal_id', $result->deal_id)
                        ->where('status', 'confirmed')
                        ->groupBy('order_id')
                        ->get();
                    $orderIds = [];
                    if (!empty($SlotDeal)) {
                        foreach ($SlotDeal as $sdeal) {
                            $orderIds[] = $sdeal['order_id'];
                        }
                    }

                    if (!empty($SlotDeal)) {
                        $orderUpdate = ['status' => 'completed'];
                        Order::whereIn('id', $orderIds)->update($orderUpdate);
                        $orderUpdate = ['status' => 'loser'];
                        OrderProduct::whereIn('order_id', $orderIds)->where('deal_id',  $data['deal_id'])->update($orderUpdate);
                        SlotDeal::whereIn('order_id', $orderIds)->where('deal_id',  $data['deal_id'])->update($orderUpdate);
                    }
                    /*  Updating order status after winner selection ends  */



                    /*  Notification */
                    $notificationData = [
                        'data' => [
                            'winning_status' => true,
                            'deal_id' => $result->deal->deal_id,
                            'order_id' => $result->order->order_id,
                            "booking_id" => $result->booking_id,
                            "customer_id" => $result->order->customer_id,
                            'slug' => 'Win'
                        ],
                        "message" => "{{Deal ID}} " . $result->deal->deal_id . " {{is Settled now. Congratulations you are winner with Slot ID}} " . $result->booking_id,
                    ];

                    $customer = Customer::where('id', $result->order->customer_id)->first();
                    Notification::send($customer, new CustomerNotification($notificationData));
                    JobsCustomerNotification::dispatch();

                    return response()->json([
                        'status' => true,
                        'messages' => ['Winner created successfully.'],
                        'data' => $result
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'messages' => ['Winner already assigned.'],
                        //'data' => $winner
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'messages' => ['No Order avaiable'],
                ], 404);
            }
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function addBotInterval($dealsDataVal)
    {
        $timeDuration   = $dealsDataVal->time_period;
        $start_date = $dealsDataVal->created_at;                                       // in hour
        $randonInterval = array();

        for ($x = 1; $x <= $timeDuration; $x++) {
            $interval_time = $x * 60;
            $newDateTime = date("Y-m-d H:i:s", strtotime("$start_date + $interval_time minutes"));
            array_push($randonInterval, $this->randomDate($start_date, $newDateTime));
            $start_date = $newDateTime;
        }
        foreach ($randonInterval as $interval) {
            TimeInterval::create([
                'deal_id'      => $dealsDataVal->id,
                'interval_time' => $interval,
            ]);
        }
    }

    function randomDate($start_date, $end_date)
    {
        $min = strtotime($start_date);
        $max = strtotime($end_date);
        $val = rand($min, $max);
        return date('Y-m-d H:i:s', $val);
    }

    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new WinnerDetail())->newQuery()->orderBy($sortBy, $sortOrder);
            $query->where('customer_id', auth()->user()->id);
            $results = $query->with(['order', 'product'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            // \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function generateDeal(array $data)
    {
        try {

            if ($data['status'] == 'active') {
                $this->createSlot($data);
                return response()->json([
                    'status' => true,
                    'messages' => ['Deal generated successfully']
                ], 201);
            } elseif ($data['status'] == 'inactive') {
                $this->deActivateProduct($data);

                return response()->json([
                    'status' => true,
                    'messages' => ['Product deactivated successfully']
                ], 200);
            }
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function deActivateProduct($data)
    {
        $where = ['id' => $data['deal_id']];
        $deal = Deal::where($where)->first();
        return Product::where(['id' => $deal->product_id])->update(['status' => 'inactive']);
    }

    public function createSlot($data)
    {
        $where = [
            'id' => $data['deal_id'],
            'status' => 'settled'
        ];
        $deal = Deal::where($where)->first();


        $dealSetting = Configure::where('configurable_type', 'App\Models\Product')->where('configurable_id', $deal->product_id)->first();
        if (empty($dealSetting)) {

            return response()->json([
                'messages' => ['Deal configuration not configured yet.'],
            ], 400);
        }
        $newDealConfiguration = (array) json_decode($dealSetting->data);
        $oldTimePeriod = $newDealConfiguration["time_period"];
        $currentDateTime = Carbon::now();
        $newDateTime = $currentDateTime->addHours($oldTimePeriod)->format('Y-m-d H:i:s');

        $newDealConfiguration["status"] = 'active';
        $newDealConfiguration["deal_id"] = getRandomIdGenerator("DL");
        $newDealConfiguration["time_period"] = $oldTimePeriod;
        $newDealConfiguration["deal_end_at"] = $newDateTime;
        $newDealConfiguration["is_bot"] = 0;

        $slot =  Slot::create([
            "total_slots" => $newDealConfiguration['total_slots'],
            "product_id" => $deal->product_id,
            "booked_slots" => 0,
            "status" => 'active'
        ]);

        $newDealConfiguration["slot_id"] = $slot->id;

        Deal::create($newDealConfiguration);

        return $slot;
    }



    public function getPredicted($data)
    {
        try {
            $where = ['slot_deals.deal_id' => $data['deal_id'], 'slot_deals.status' => 'confirmed'];
            if (!empty($data['winner_option'])) {
                if ($data['winner_option'] == '1') {
                    $where += ['slot_deals.is_bot' => '0'];
                } elseif ($data['winner_option'] == '2') {
                    $where += ['slot_deals.is_bot' => '1'];
                }
            }
            $select = [
                'slot_deals.id',
                'slot_deals.order_id',
                'slot_deals.slot_id',
                'slot_deals.deal_id',
                'slot_deals.booking_id',
            ];
            $result = SlotDeal::select($select)->with(['order'])
                ->leftJoin('orders', 'orders.id', '=', 'slot_deals.order_id')
                ->inRandomOrder()
                ->where($where)
                ->select('slot_deals.*');

            $itemsPaginated = $result->first();

            if (empty($itemsPaginated)) {
                return response()->json([
                    'messages' => ['No winner can select with the combination.'],
                ], 404);
            }

            $data = [
                'deal_id' =>  $itemsPaginated->deal_id,
                'order_id' =>  $itemsPaginated->order_id
            ];

            $itemsTransformed = [
                "id" => $itemsPaginated->id,
                "booking_id" => $itemsPaginated->booking_id,
                "id" => $itemsPaginated->id,
                "customer" => $this->getDealCustomer($data, $itemsPaginated->order->customer, $itemsPaginated->booking_id),
                "deal" => $itemsPaginated->deal
            ];

            return response()->json($itemsTransformed, 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function getDealCustomer($data, $customer, $booking_id)
    {

        $totalSlot = OrderProduct::where('deal_id', $data['deal_id'])->where('customer_id', $customer->id)->whereIn('status', ['confirmed'])->sum('slots');
        $customer->system = Bot::where(['type' => 'customer', 'customer_id' => $customer->id])->count();
        $customer->no_of_slots = $totalSlot;
        $customer->booking_id = $booking_id;

        return $customer;
    }
    public function winnerList($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'winner_details.created_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new WinnerDetail())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->slot_id, function ($query) use ($request) {
                $query->where('winner_details.slot_id', $request->slot_id);
            });
            $query->when($request->deal_id, function ($query) use ($request) {
                $query->where('winner_details.deal_id', $request->deal_id);
            });
            $query->when($request->customer_id, function ($query) use ($request) {
                $query->where('winner_details.customer_id', $request->customer_id);
            });
            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('winner_details.created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('winner_details.created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });



            $itemsPaginated = $query
                ->leftJoin('shippings', 'shippings.order_id', 'winner_details.order_id',function($q){
                    $q->orderBy('id','desc');
                })
                ->with(['order', 'product'])
                ->select('winner_details.*','shippings.status as ship_status')
                ->paginate($perPage, ['*'], 'page', $page);



            $itemsTransformed = $itemsPaginated
                ->getCollection()
                ->map(function ($item) {
                    $dl = Deal::with('currency:id,symbol,code')->where('id',$item->deal_id)->first();
                    return [
                        "id" => $item->id,
                        "customer_id" => $item->customer_id,
                        "member_id" => Customer::where('id',$item->customer_id)->first()->member_ID,
                        "booking_id" => $item->booking_id,
                        "order_id" => $item->order_id,
                        "deal_id" => $item->deal_id,
                        "currency" => $dl->currency->symbol,
                        "deal_random_id" => $dl->deal_id,
                        "slot_id" => $item->slot_id,
                        "created_at" => $item->created_at,
                        "updated_at" => $item->updated_at,
                        "deleted_at" => $item->deleted_at,
                        "order" => $item->order,
                        "product" => $item->product,
                        "shipping_status" => $item->ship_status,
                        "member_count" => $this->getCustomerCount($item->deal_id, 0),
                        "bot_count" => $this->getCustomerCount($item->deal_id, 1),
                        "revenue" => $this->getRevenue($item->deal_id),
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
            return generalErrorResponse($e);
        }
    }
    public function getRevenue($dealId)
    {
        return  Settlement::select(['actual_cost', 'received_amount', 'result_type', 'type', 'profit_loss_amount'])->where('deal_id', $dealId)->first();
    }
    public function getCustomerCount($dealId, $isBot)
    {
        $count = 0;
        $customerIds = OrderProduct::where('deal_id', $dealId)->whereNotIn('status', ['reserved', 'canceled'])->groupBy('customer_id')->get()->pluck('customer_id');
        if ($isBot) {
            $count =  Bot::whereIn('customer_id', $customerIds)->count();
        } else {
            $customer_count =  Customer::whereIn('customers.id', $customerIds)->count();
            $count =  Bot::whereIn('customer_id', $customerIds)->count();
             $count = $customer_count - $count;
        }
        return $count;
    }
}
