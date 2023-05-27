<?php

namespace App\Console\Commands;

use App\Jobs\ClearOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelled All Expired Orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Cancel:order Cron is working fine!");
        ClearOrder::dispatch();
    }
}
