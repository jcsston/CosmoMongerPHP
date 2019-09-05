<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseShipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_ship', function (Blueprint $table) {
            $table->bigIncrements('base_ship_id');
            $table->string("name", 255);
            $table->integer("base_price");
            $table->integer("cargo_space");
            $table->integer("initial_jump_drive_id");
            $table->integer("initial_weapon_id");
            $table->integer("initial_shield_id");
            $table->integer("hit_factor");
            $table->integer("level");
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
        Schema::dropIfExists('base_ship');
    }
}
