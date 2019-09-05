<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCombatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combat', function (Blueprint $table) {
            $table->bigIncrements('combat_id');
            $table->integer("attacker_ship_id");
            $table->integer("defender_ship_id");
            $table->integer("turn");
            $table->integer("turn_points_left");
            $table->boolean("surrendered");
            $table->boolean("cargo_jettisoned");
            $table->integer("status");
            $table->dateTime("last_action_time");
            $table->integer("credit_looted");
            $table->boolean("search");
            $table->integer("attacker_hits");
            $table->integer("attacker_misses");
            $table->integer("defender_hits");
            $table->integer("defender_misses");
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
        Schema::dropIfExists('combat');
    }
}
