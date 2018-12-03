<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctBcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_bcs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('COST_CENTER', 10);
            $table->string('BC_STAFF_ID', 10);
            $table->string('BC_STAFF_NAME', 100);
            $table->string('STATUS', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_bcs');
    }
}
