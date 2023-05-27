<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\SlotDeal;
use App\Models\WinnerDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerNotification as CustomNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomerNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $winnerData = WinnerDetail::orderBy('id', 'desc')->first();
        $dealId = $winnerData->deal_id;
        $deal = Deal::where(['id' => $dealId])->first();

        $customerId = $winnerData->customer_id;
        $slotDeals = SlotDeal::with(['order'])->where(['deal_id' => $dealId])->groupBy('deal_id', 'order_id')->get();
        if (!empty($slotDeals)) {
            $sentCustomers = [];
            foreach ($slotDeals as $result) {

                if ($customerId !== $result->order->customer_id && !in_array($result->order->customer_id, $sentCustomers)) {

                    if(!empty($deal)){
                        $dealId = $deal->deal_id;
                       /*  Notification */
                       $notificationData = [
                           'data' => [
                               'winning_status' => false,
                               'order_id' => $result->order_id,
                               "booking_id" => $result->booking_id,
                               "customer_id" => $result->order->customer_id,
                               'slug' => 'Lose'
                           ],
                           "message" => "{{Deal ID}} ".$dealId." {{is Settled now. Unfortunately You are not winner, better luck next time}}",
                       ];
                       array_push($sentCustomers, $result->order->customer_id);
                       $customers[]=$result->order->customer_id;
                       $customer = Customer::where('id', $result->order->customer_id)->first();
                       Notification::send($customer, new CustomNotification($notificationData));


                    }

                }
            }


        }
    }
}
