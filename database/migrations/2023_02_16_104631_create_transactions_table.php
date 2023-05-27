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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ID')->unique()->comment('unique ID for each transaction');
            $table->foreignId('member_id')->constrained('customers')->comment('Ref on customers table');
            $table->enum('transaction_type', ['transfer_in', 'transfer_out']);
            $table->string('order_id')->nullable();
            $table->double('amount',20,8)->default(0);
            $table->foreignId('currency_id')->constrained('currencies')->comment('Ref on currencies table');
            $table->string('message')->nullable();
            $table->enum('status', ['Debit', 'Credit', 'Reject','Review','Approve']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
