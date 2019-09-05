<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ship', function (Blueprint $table) {
            $table->bigIncrements('ship_id');
            $table->integer('base_ship_id');
            $table->integer('system_id');
            $table->integer('weapon_id');
            $table->integer('jump_drive_id');
            $table->integer('shield_id');
            $table->integer('damage_engine');
            $table->integer('damage_weapon');
            $table->integer('damage_hull');
            $table->integer('target_system_id')->nullable();
            $table->integer('current_jump_drive_charge');
            $table->timestamp('target_system_arrival_time')->nullable();
            $table->integer('credits');
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
        Schema::dropIfExists('ship');
    }
}
