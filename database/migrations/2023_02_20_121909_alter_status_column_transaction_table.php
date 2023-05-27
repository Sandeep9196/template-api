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
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` CHANGE `transaction_type` `transaction_type` ENUM('transfer_in','transfer_out','withdraw') NULL DEFAULT NULL ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` CHANGE `transaction_type` `transaction_type` ENUM('transfer_in','transfer_out') NULL DEFAULT NULL ");
        }
    }
};
