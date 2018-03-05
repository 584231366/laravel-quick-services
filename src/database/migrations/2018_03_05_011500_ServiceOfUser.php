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
            $table->integer('user_id');
            $table->integer('service_id');
            $table->datetime('start_at')->default('2018-01-01');
            $table->datetime('expirated_at')->default('2018-01-01');
            $table->integer('times')->default(0);
            $table->integer('tag');
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
