<?php

namespace App\Jobs;

use App\Models\Deal;
use App\Models\Slot;
use App\Models\SlotDeal;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearDeals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $status = false;
        $currentDateTime = Carbon::now();

        $dealsDetails = Deal::whereIn('status', ['active', 'inactive', 'expired'])->orderBy('created_at', 'desc')->get();



        foreach ($dealsDetails as $dealsData) {
            $slotsDetails = Slot::where('id', $dealsData->slot_id)->first();
            $dealEndDate = Carbon::parse($dealsData->deal_end_at);
            $confirmSlots = SlotDeal::where('deal_id', $dealsData->id)->where('status', 'confirmed')->count();
            Slot::where('id', $slotsDetails->id)->update(['booked_slots' => $confirmSlots]);
            $updateData = [];

            if ($slotsDetails->total_slots <= $confirmSlots) {
                $updateData = ['status' => 'inactive'];
                $status = true;
            } else if ($currentDateTime->greaterThan($dealEndDate) || empty($dealsData->deal_end_at)) {
                if ($slotsDetails->total_slots <= $confirmSlots) {
                    $updateData = ['status' => 'inactive'];
                } else {
                    $updateData = ['status' => 'expired'];
                }
            } else {
                $updateData = ['status' => 'active'];
            }

            Deal::where('id', $dealsData->id)->update($updateData);
        }
        return $status;
    }
}
