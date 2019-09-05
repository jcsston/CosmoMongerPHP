<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player', function (Blueprint $table) {
            $table->bigIncrements('player_id');
            $table->integer("user_id");
            $table->integer("race_id");
            $table->integer("ship_id");
            $table->string("name", 255);
            $table->integer("bank_credits");
            $table->double("time_played");
            $table->timestamp("last_played");
            $table->integer("net_worth");
            $table->integer("ships_destroyed");
            $table->integer("forced_surrenders");
            $table->integer("forced_flees");
            $table->integer("cargo_looted_worth");
            $table->integer("ships_lost");
            $table->integer("surrender_count");
            $table->integer("flee_count");
            $table->integer("cargo_lost_worth");
            $table->boolean("alive");
            $table->integer("last_record_snapshot_age");;
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
        Schema::dropIfExists('player');
    }
}
