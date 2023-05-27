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
        DB::statement("ALTER TABLE `order_product` CHANGE COLUMN `status` `status` ENUM('reserved','confirmed','canceled','loser','completed') NULL DEFAULT 'reserved'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `order_product` CHANGE COLUMN `type` `status` ENUM('winner','reserved','confirmed','canceled','loser','completed') NULL DEFAULT 'reserved'");
    }
};
