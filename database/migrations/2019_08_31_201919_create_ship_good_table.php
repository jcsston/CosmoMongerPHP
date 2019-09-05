<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ship_good', function (Blueprint $table) {
            $table->bigIncrements('ship_good_id');
            $table->integer('ship_id');
            $table->integer('good_id');
            $table->integer('quantity');
            $table->unique(['ship_id', 'good_id']);
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
        Schema::dropIfExists('ship_good');
    }
}
