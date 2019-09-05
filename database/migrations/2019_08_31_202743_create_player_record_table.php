<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_record', function (Blueprint $table) {
            $table->bigIncrements('player_record_id');
            $table->integer('player_id');
            $table->datetime('record_time');
            $table->double('time_played');
            $table->integer('net_worth');
            $table->integer("ships_destroyed");
            $table->integer("forced_surrenders");
            $table->integer("forced_flees");
            $table->integer("cargo_looted_worth");
            $table->integer("ships_lost");
            $table->integer("surrender_count");
            $table->integer("flee_count");
            $table->integer("cargo_lost_worth");
            $table->double("distance_traveled");
            $table->integer("goods_traded");
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
        Schema::dropIfExists('player_record');
    }
}
