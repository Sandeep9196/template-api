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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->comment('reference to customer table');
            $table->string('order_id');
            $table->string('total_amount');
            $table->string('total_products');
            $table->string('total_quantity');
            $table->enum('status', ['reserved', 'confirmed', 'canceled'])->default('reserved');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['customer_id','order_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
