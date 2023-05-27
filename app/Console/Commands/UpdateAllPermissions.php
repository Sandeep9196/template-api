<?php

namespace App\Console\Commands;

use App\Helpers\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateAllPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update All Permissions with respect to entire project';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseUrl = config('constant.SERVER_URL');
        Artisan::call('update:permissions');
        $routes['masters'] = json_decode(Http::get($baseUrl.'/api/masters/getData?lang_id=1&type=GET_DATA_PERMISSIONS'));
        $routes['products'] = json_decode(Http::get($baseUrl.'/api/products/getData?lang_id=1&type=GET_DATA_PERMISSIONS'));
        $routes['inventory'] = json_decode(Http::get($baseUrl.'/api/inventory/getData?lang_id=1&type=GET_DATA_PERMISSIONS'));
        $routes['deals'] = json_decode(Http::get($baseUrl.'/api/deals/getData?lang_id=1&type=GET_DATA_PERMISSIONS'));
        $routes['orders'] = json_decode(Http::get($baseUrl.'/api/orders/getData?lang_id=1&type=GET_DATA_PERMISSIONS'));
        $excludedRoutes = ['ignition.executeSolution', 'ignition.healthCheck', 'ignition.updateConfig', 'sanctum.csrf-cookie'];
        $permissions = [];

        foreach ($routes as $routeData) {
            foreach($routeData as $route){
                $name = trim($route->name);
                $url = trim($route->url);

                if (! $name || in_array($name, $excludedRoutes)) {
                    continue;
                }

                try {
                    // throws an exception rather than returning null
                    $permission = Permission::findByName($name, 'web');
                    array_push($permissions, $permission->name);
                    // echo 'find- ' . $permission->name . "\n";
                } catch (\Exception$e) {
                    $permission = Permission::create(['name' => $name, 'url' => $url, 'guard_name' => 'web']);
                    array_push($permissions, $permission->name);
                    echo 'create- '.$permission->name."\n";
                }
            }

        }

        try {
            echo "Sync super admin permissions...\n";

            $superAdmin = Role::findByName('Admin', 'web');
            $superAdmin->syncPermissions(array_unique($permissions));

            echo "Super admin permissions updated.\n";

            echo "Clean up old/outdated permissions.\n";
            // Clean up old and unused permissions
            $allPermissions = Permission::all();
            foreach ($allPermissions as $p) {
                if (! in_array($p->name, array_unique($permissions))) {
                    try {
                        $p->delete();
                        echo 'Delete - '.$p->name."\n";
                    } catch (\Exception$e) {
                        echo $e->getMessage()."\n";
                    }
                }
            }
            echo "All done.\n";
        } catch (\Exception$e) {
            echo $e->getMessage();
        }
    }
}
