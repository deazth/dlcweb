<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulkInactivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_inactives', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('A_STAFF_ID', 10);
            $table->string('SERIAL_NO')->nullable();
            $table->string('TAG_NO')->nullable();
            $table->integer('DEVICE_ID')->nullable();
            $table->string('DEVICE_TYPE', 10);
            $table->string('STATUS', 2)->default('N');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_inactives');
    }
}
