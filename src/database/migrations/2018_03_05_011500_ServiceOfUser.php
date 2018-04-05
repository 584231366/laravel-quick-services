<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceOfUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_of_user', function (Blueprint $table) {
            $table->integer('user_id')->comment('关联用户ID');
            $table->integer('service_id')->comment('服务ID');
            $table->datetime('start_at')->nullable()->comment('服务开始时间');
            $table->datetime('expirated_at')->nullable()->comment('服务过期时间');
            $table->integer('times')->default(0)->comment('服务次数');
            $table->integer('last_id')->comment('关联用户的最新记录ID');
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
        Schema::dropIfExists('service_of_user');
    }
}
