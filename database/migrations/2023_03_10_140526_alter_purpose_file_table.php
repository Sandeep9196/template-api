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
        Schema::table('files', function (Blueprint $table) {
            DB::statement("ALTER TABLE `files` CHANGE COLUMN `purpose` `purpose` tinyInt NOT NULL COLLATE 'utf8mb4_unicode_ci' COMMENT '1 : Setting Logo,
            2 : Twitter Icon,
            3 : Pinterest Icon,
            4 : Facebook Icon,
            5 : Youtube Icon,
            6 : Instagram Icon,
            7 : QQ Icon,
            8 : Skype Icon,
            9 : Telegram Icon,
            10 : Whatsapp Icon,
            11 : H5 Logo'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            DB::statement("ALTER TABLE `files` CHANGE COLUMN `purpose` `purpose` tinyInt NOT NULL COLLATE 'utf8mb4_unicode_ci' COMMENT '1 : Setting Logo,
            2 : Twitter Icon,
            3 : Pinterest Icon,
            4 : Facebook Icon,
            5 : Youtube Icon,
            6 : Instagram Icon,
            7 : QQ Icon,
            8 : Skype Icon,
            9 : Telegram Icon,
            10 : Whatsapp Icon
            '");

        });
    }
};
