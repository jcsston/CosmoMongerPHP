<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemShipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_ship', function (Blueprint $table) {
            $table->bigIncrements('system_ship_id');
            $table->integer('system_id');
            $table->integer('base_ship_id');
            $table->integer('quantity');
            $table->double('price_multiplier');
            $table->unique(['system_id', 'base_ship_id']);
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
        Schema::dropIfExists('system_ship');
    }
}
