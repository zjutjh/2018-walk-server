<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('num')->comment('队伍人数');
            $table->enum('start_campus', ['屏峰', '朝晖'])->comment('出发校区');
            $table->text('description')->comment('队伍简介');
            $table->integer('captain_id')->comment('队长id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yx_groups');
    }
}
