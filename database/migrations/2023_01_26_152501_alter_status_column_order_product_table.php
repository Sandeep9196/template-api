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
        DB::statement("ALTER TABLE order_product MODIFY COLUMN status ENUM('reserved','confirmed','canceled','completed') DEFAULT 'reserved' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE order_product MODIFY COLUMN status ENUM('reserved','confirmed','canceled') DEFAULT 'reserved'  ");
    }
};
