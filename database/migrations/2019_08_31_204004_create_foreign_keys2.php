<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message', function (Blueprint $table) {
            $table->foreign('recipient_user_id')->references('id')->on('users');
            $table->foreign('sender_user_id')->references('id')->on('users');
        });

        Schema::table('npc', function (Blueprint $table) {
            $table->foreign('race_id')->references('race_id')->on('race');
            $table->foreign('ship_id')->references('ship_id')->on('ship');
            $table->foreign('last_attacked_ship_id')->references('ship_id')->on('ship');
            $table->foreign('last_visited_system_id')->references('system_id')->on('system');
        });

        Schema::table('player', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('race_id')->references('race_id')->on('race');
            $table->foreign('ship_id')->references('ship_id')->on('ship');
        });

        Schema::table('race', function (Blueprint $table) {
            $table->foreign('home_system_id')->references('system_id')->on('system');
            $table->foreign('racial_enemy_id')->references('race_id')->on('race');
            $table->foreign('racial_preference_id')->references('race_id')->on('race');
        });

        Schema::table('ship', function (Blueprint $table) {
            $table->foreign('base_ship_id')->references('base_ship_id')->on('base_ship');
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('weapon_id')->references('weapon_id')->on('weapon');
            $table->foreign('shield_id')->references('shield_id')->on('shield');
            $table->foreign('jump_drive_id')->references('jump_drive_id')->on('jump_drive');
            $table->foreign('target_system_id')->references('system_id')->on('system');
        });

        Schema::table('ship_good', function (Blueprint $table) {
            $table->foreign('ship_id')->references('ship_id')->on('ship');
            $table->foreign('good_id')->references('good_id')->on('good');
        });

        Schema::table('system', function (Blueprint $table) {
            $table->foreign('race_id')->references('race_id')->on('race');
        });

        Schema::table('system_good', function (Blueprint $table) {
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('good_id')->references('good_id')->on('good');
        });

        Schema::table('system_jump_drive_upgrade', function (Blueprint $table) {
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('jump_drive_id')->references('jump_drive_id')->on('jump_drive');
        });

        Schema::table('system_shield_upgrade', function (Blueprint $table) {
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('shield_id')->references('shield_id')->on('shield');
        });

        Schema::table('system_weapon_upgrade', function (Blueprint $table) {
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('weapon_id')->references('weapon_id')->on('weapon');
        });

        Schema::table('system_ship', function (Blueprint $table) {
            $table->foreign('system_id')->references('system_id')->on('system');
            $table->foreign('base_ship_id')->references('base_ship_id')->on('base_ship');
        });

        Schema::table('combat_good', function (Blueprint $table) {
            $table->foreign('combat_id')->references('combat_id')->on('combat');
            $table->foreign('good_id')->references('good_id')->on('good');
        });

        Schema::table('player_record', function (Blueprint $table) {
            $table->foreign('player_id')->references('player_id')->on('player');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // TODO
    }
}
