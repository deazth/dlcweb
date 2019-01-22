<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('STAFF_ID', 10);
            $table->string('ACTION', 50);
            $table->string('DETAILS', 300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_logs');
    }
}
