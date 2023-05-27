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
        if(!Schema::hasTable('menus')){
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('type_id')->nullable()->constrained();
                $table->foreignId('group_id')->nullable()->constrained();
                $table->string('slug')->comment('part of a URL that is easy-to-read');
                $table->enum('status',['active','inactive'])->default('active');
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['slug','deleted_at']);
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
        Schema::dropIfExists('menus');
    }
};
