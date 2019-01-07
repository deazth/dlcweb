<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('STAFF_ID', 10);
            $table->string('STAFF_NAME', 50);
            $table->string('COST_CENTER', 10);
            $table->string('OFFICE_ADDR', 500);
            $table->string('CONTACT_NO', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_users');
    }
}
