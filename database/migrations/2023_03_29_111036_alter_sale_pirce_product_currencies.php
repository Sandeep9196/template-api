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
        Schema::table('products', function (Blueprint $table) {
            DB::statement("ALTER TABLE `product_currencies` CHANGE COLUMN `sale_price` `sale_price` DOUBLE DEFAULT 0 COLLATE 'utf8mb4_unicode_ci'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `product_currencies` CHANGE COLUMN `sale_price` `sale_price` DOUBLE DEFAULT 0 COLLATE 'utf8mb4_unicode_ci'");
    }
};
