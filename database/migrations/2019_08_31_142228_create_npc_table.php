<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('npc', function (Blueprint $table) {
            $table->bigIncrements('npc_id');
            $table->integer("npc_type_id");
            $table->string("name", 255);
            $table->integer("race_id")->nullable();
            $table->integer("ship_id")->nullable();
            $table->integer("aggression");
            $table->timestamp("next_action_time");
            $table->timestamp("next_travel_time")->nullable();
            $table->integer("last_visited_system_id")->nullable();
            $table->integer("last_attacked_ship_id")->nullable();
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
        Schema::dropIfExists('npc');
    }
}
