<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_good', function (Blueprint $table) {
            $table->bigIncrements('system_good_id');
            $table->integer('system_id');
            $table->integer('good_id');
            $table->integer('quantity');
            $table->double('price_multiplier');
            $table->double('production_factor');
            $table->double('consumption_factor');
            $table->integer('demand');
            $table->unique(['system_id', 'good_id']);
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
        Schema::dropIfExists('system_good');
    }
}
