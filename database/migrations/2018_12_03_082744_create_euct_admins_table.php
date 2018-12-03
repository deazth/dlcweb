<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('STAFF_ID', 10);
            $table->string('STAFF_NAME', 100);
            $table->integer('ROLE_TYPE');
            $table->string('REMARK', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_admins');
    }
}
