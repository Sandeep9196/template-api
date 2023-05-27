<?php

namespace App\Console\Commands;

use App\Helpers\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class updateAllMigrationFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Fresh All Service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            echo "Sync migrations:fresh...\n";
            $baseUrl = config('constant.SERVER_URL');
            json_decode(Http::get($baseUrl . '/api/masters/getData?lang_id=1&type=UPDATE_MIGRATION_FRESH'));
            echo "masters migration done...\n";
            json_decode(Http::get($baseUrl . '/api/products/getData?lang_id=1&type=UPDATE_MIGRATION_FRESH'));
            echo "products migration done...\n";
            json_decode(Http::get($baseUrl . '/api/inventory/getData?lang_id=1&type=UPDATE_MIGRATION_FRESH'));
            echo "inventory migration done...\n";
            json_decode(Http::get($baseUrl . '/api/deals/getData?lang_id=1&type=UPDATE_MIGRATION_FRESH'));
            echo "deals migration done...\n";
            json_decode(Http::get($baseUrl . '/api/orders/getData?lang_id=1&type=UPDATE_MIGRATION_FRESH'));
            echo "orders migration done.....";
            Artisan::call('update:all-permissions');
            echo "All done.\n";
        } catch (\Exception$e) {
            echo $e->getMessage();
        }
    }
}
