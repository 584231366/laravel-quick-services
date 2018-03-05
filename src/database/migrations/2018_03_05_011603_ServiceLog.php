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
            $table->increments('service_log_id');
            $table->integer('user_id');
            $table->integer('service_id');
            $table->integer('service_log_type');
            $table->integer('days')->default(0);
            $table->integer('times')->default(0);
            $table->string('memo');
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
