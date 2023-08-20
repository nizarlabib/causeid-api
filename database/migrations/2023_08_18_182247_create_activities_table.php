<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->string('activity_picture')->nullable();;
            $table->string('activity_type');
            $table->float('activity_kilometers');
            $table->integer('activity_hours');
            $table->integer('activity_minutes');
            $table->integer('activity_seconds');
            $table->datetime('activity_datetime');
            $table->string('race_ids')->unsigned()->nullable();
            $table->foreignId('user_id')->unsigned();
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
        Schema::dropIfExists('activities');
    }
}
