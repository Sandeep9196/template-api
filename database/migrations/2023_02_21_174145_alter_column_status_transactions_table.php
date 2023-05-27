<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` CHANGE COLUMN `status` `status` ENUM('Debit','Credit','Reject','Review','Approve','Pending','Success','Fail') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `message`");
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` CHANGE COLUMN `status` `status` ENUM('Debit','Credit','Reject','Review','Approve') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `message`");
        }
        
    }
};
