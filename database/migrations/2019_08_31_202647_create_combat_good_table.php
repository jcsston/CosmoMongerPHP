<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCombatGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combat_good', function (Blueprint $table) {
            $table->bigIncrements('combat_good_id');
            $table->integer('combat_id');
            $table->integer('good_id');
            $table->integer('quantity');
            $table->integer('quantity_picked_up');
            $table->unique(['combat_id', 'good_id']);
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
        Schema::dropIfExists('combat_good');
    }
}
