<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->enum('sex', ['男', '女'])->nullable();
            $table->enum('campus', ['屏峰', '朝晖'])->comment('校区');
            $table->string('phone')->nullable()->comment('电话号码');
            $table->string('id_card')->nullable()->comment('身份证');
            $table->string('openid')->unique()->comment('微信openid');
            $table->string('qq')->nullable()->comment('联系qq');

            $table->boolean('have_team')->default(false)->comment('是否组队');
            $table->integer('yx_group_id')->nuallable()->comment('队伍编号');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
