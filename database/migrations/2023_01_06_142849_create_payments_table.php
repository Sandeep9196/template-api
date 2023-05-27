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
        ini_set('memory_limit', -1);
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('order_product_ids')->nullable();
            $table->string('external_order_ID')->nullable();
            $table->string('external_transaction_ID')->nullable();
            $table->string('payment_id');
            $table->string('customer_id');
            $table->enum('payment_type',['w','p','b'])->comment('w: pay by wallet, p: pay by third party, b: pay by both w and p');
            $table->string('amount')->default(0);
            $table->string('wallet_amount')->nullable()->default(0);
            $table->string('provider');
            $table->json('request_data')->nullable()->comment("the data that will be sent to pay in third party payment");
            $table->json('response_data')->nullable()->comment("the data that is the response from third party payment");
            $table->string('message')->nullable()->comment("the data that is the response from third party payment");
            $table->enum('status',['pending','complete','fail'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['order_id','payment_id','customer_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
