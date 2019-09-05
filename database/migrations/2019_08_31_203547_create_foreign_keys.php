<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('base_ship', function (Blueprint $table) {
            $table->foreign('initial_jump_drive_id')->references('jump_drive_id')->on('jump_drive');
            $table->foreign('initial_weapon_id')->references('weapon_id')->on('weapon');
            $table->foreign('initial_shield_id')->references('shield_id')->on('shield');
        });

        Schema::table('buddy_list', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('friend_id')->references('id')->on('users');
        });

        Schema::table('ignore_list', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('ant_friend_id')->references('id')->on('users');
        });

        Schema::table('combat', function (Blueprint $table) {
            $table->foreign('attacker_ship_id')->references('ship_id')->on('ship');
            $table->foreign('defender_ship_id')->references('ship_id')->on('ship');
            $table->foreign('combat_id')->references('combat_id')->on('combat_good');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // TODO: drop fks
    }
}
