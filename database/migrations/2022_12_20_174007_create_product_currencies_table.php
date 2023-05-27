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
        Schema::create('product_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->comment('reference to products table');
            $table->foreignId('currency_id')->constrained()->comment('reference to currencies table');
            $table->string('price');
            $table->string('sale_price');
            $table->string('purchase_price');
            $table->timestamps();

            //$table->index(['product_id','currency_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_currencies');
    }
};
