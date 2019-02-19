<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('TAG_NO');
            $table->string('SERIAL_NUMBER');
            $table->string('MODEL');
            $table->string('BRAND');
            $table->string('CATEGORY');

            $table->string('STATUS');
            /*
              List of possible status:
              New         - newly received from vendor's list
              Received    - physically received the device
              Dispatched  - the device has been given to staff
             */

            $table->string('BATCH_NO');
            $table->string('EQUIP_TYPE');
            $table->string('WARRANTY_DATE');
            $table->string('ADDED_BY');
            $table->string('RECEIVED_BY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_stores');
    }
}
