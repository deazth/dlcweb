<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('ORDER_NO', 15);
            $table->string('ORDER_TYPE', 20);
            $table->string('TAG_NO', 20);
            $table->string('REQ_STAFF_ID', 10);
            $table->string('STATUS', 10);
            $table->string('ORD_REMARK', 300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_orders');
    }
}
