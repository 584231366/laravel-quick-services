<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_log', function (Blueprint $table) {
            $table->increments('service_log_id')->comment('日志ID');
            $table->integer('user_id')->comment('关联用户ID');
            $table->integer('service_id')->comment('关联服务ID');
            $table->integer('service_log_type')->comment('日志类型 0:增 1:减');
            $table->integer('days')->default(0)->comment('天数');
            $table->integer('times')->default(0)->comment('次数');
            $table->string('memo')->nullable()->comment('备注');
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
        Schema::dropIfExists('service_options');
    }
}
