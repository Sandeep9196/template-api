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

class UpdateAdminDashboardMissingKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $newData, $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($locale , $newData, $file)
    {
        //
        $this->newData = $newData;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists($this->file)) {
            $handle = fopen($this->file, 'w');
        }
        // reading existing missing keys
        $jsonString = file_get_contents($this->file);

        if(json_decode($jsonString, true))
            $datas = json_decode($jsonString, true);
        else
            $datas = [];
        if(getType($datas) != 'array')
            $datas = (array) $datas;

        // \Log::debug("newdatas:");
        // \Log::debug((array)$this->newData);
        // \Log::debug("datas:");
        // \Log::debug($datas);
        foreach($this->newData as $key => $value) {
            if($key && !@$datas[$key])
                $datas[$key] = $value;

        }

        //adding more missing keys
        $newJsonString = json_encode($datas, JSON_PRETTY_PRINT);
        // \Log::debug("newJsonString:");
        // \Log::debug($newJsonString);
        file_put_contents($this->file, stripslashes($newJsonString));
        @$handle?fclose($handle):'';
    }
}
