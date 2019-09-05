<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race', function (Blueprint $table) {
            $table->bigIncrements('race_id');
            $table->string("name", 255);
            $table->integer("weaons");
            $table->integer("shields");
            $table->integer("engine");
            $table->integer("accuracy");
            $table->integer("home_system_id");
            $table->string("description", 1000);
            $table->integer("racial_enemy_id")->nullable();
            $table->integer("racial_preference_id")->nullable();
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
        Schema::dropIfExists('race');
    }
}
