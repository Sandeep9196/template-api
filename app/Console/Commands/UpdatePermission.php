<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update role permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $excludedRoutes = ['ignition.executeSolution', 'ignition.healthCheck', 'ignition.updateConfig', 'sanctum.csrf-cookie'];
        $permissions = [];

        echo "Clean up old/outdated permissions.\n";
        // Clean up old and unused permissions
        $permissions = Permission::get();
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $permissionId = $permission['id'];
                DB::statement("DELETE FROM role_has_permissions WHERE permission_id = $permissionId AND role_id = '1' ");
            }
        }
        echo "All done.\n";

        $permissions = [];

        foreach ($routes as $route) {
            $name = trim($route->getName());

            if (!$name || in_array($name, $excludedRoutes)) {
                continue;
            }

            try {
                // throws an exception rather than returning null
                $permission = Permission::findByName($name, 'web');
                // dd($permission->name);
                array_push($permissions, $permission->name);
                // echo 'find- ' . $permission->name . "\n";
            } catch (\Exception $e) {
                $permission = Permission::create(['name' => $name, 'guard_name' => 'web']);
                array_push($permissions, $permission->name);
                echo 'create- ' . $permission->name . "\n";
            }
        }


        try {
            echo "Sync super admin permissions...\n";

            $superAdmin = Role::findByName('Admin', 'web');
            $permissions = Permission::get();
            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    $permissionData = [
                        'permission_id' => $permission['id'],
                        'role_id' => $superAdmin->id
                    ];
                    $permissionId = $permission['id'];
                    $result = DB::select("SELECT * FROM role_has_permissions WHERE role_id = '1' AND permission_id = $permissionId ");
                    if (empty($result)) {
                        DB::table('role_has_permissions')->insert($permissionData);
                    }
                }
            }

            echo "Super admin permissions updated.\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
