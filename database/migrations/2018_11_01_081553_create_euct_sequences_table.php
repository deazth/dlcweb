<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEuctSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('euct_sequences', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type', 20);
            $table->integer('curnum');
            $table->integer('numlen');
            $table->string('prefix', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('euct_sequences');
    }
}
