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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->comment('reference to customer table');
            $table->string('order_id')->comment('reference to order table');
            $table->foreignId('product_id')->constrained()->comment('reference to products table');
            $table->string('amount')->comment('reference to order table');
            $table->string('quantity')->nullable()->comment('reference to order table');
            $table->enum('status', ['reserved', 'confirmed', 'canceled'])->default('reserved');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['customer_id','order_id','product_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product');
    }
};
