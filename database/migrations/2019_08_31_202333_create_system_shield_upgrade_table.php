<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemShieldUpgradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_shield_upgrade', function (Blueprint $table) {
            $table->bigIncrements('system_shield_upgrade_id');
            $table->integer('system_id');
            $table->integer('shield_id');
            $table->integer('quantity');
            $table->double('price_multiplier');
            $table->unique(['system_id', 'shield_id']);
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
        Schema::dropIfExists('system_shield_upgrade');
    }
}
