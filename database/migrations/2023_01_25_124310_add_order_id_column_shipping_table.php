<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        if (Schema::hasTable('shippings')) {
            Schema::table('shippings', function (Blueprint $table) {
                $table->bigInteger('order_id')->nullable()->after('booking_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('shippings')) {
            Schema::table('shippings', function (Blueprint $table) {
                $table->dropColumn('order_id');
            });
        }
    }
};
