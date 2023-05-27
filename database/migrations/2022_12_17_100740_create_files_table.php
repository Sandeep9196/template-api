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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fileable_id');
            $table->string('fileable_type');
            $table->text('path');
            $table->enum('type',['image','file','audio','video']);
            $table->tinyInteger('purpose')->default(0)->comment('1 : Setting Logo,
                                                                2 : Twitter Icon,
                                                                3 : Pinterest Icon,
                                                                4 : Facebook Icon,
                                                                5 : Youtube Icon,
                                                                6 : Instagram Icon,
                                                                7 : QQ Icon,
                                                                8 : Skype Icon,
                                                                9 : Telegram Icon,
                                                                10 : Whatsapp Icon,');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['fileable_id','fileable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
