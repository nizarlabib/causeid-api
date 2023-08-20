<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('race_name');
            $table->string('race_picture')->nullable();
            $table->datetime('race_startdatetime');
            $table->datetime('race_enddatetime');
            $table->datetime('race_activitystartdatetime');
            $table->datetime('race_activityenddatetime');
            $table->text('race_description');
            $table->integer('race_finishkilometer');
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
        Schema::dropIfExists('races');
    }
}
