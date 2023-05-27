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
            DB::statement("ALTER TABLE `products` CHANGE COLUMN `discount` `discount` Int DEFAULT 0 COLLATE 'utf8mb4_unicode_ci'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            DB::statement("ALTER TABLE `products` CHANGE COLUMN `discount` `discount` Int NOT NULL COLLATE 'utf8mb4_unicode_ci'");
        });
    }
};
