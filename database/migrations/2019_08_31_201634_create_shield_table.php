<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shield', function (Blueprint $table) {
            $table->bigIncrements('shield_id');
            $table->string('name', 255);
            $table->integer('stength');
            $table->integer('base_price');
            $table->integer('cargo_cost');
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
        Schema::dropIfExists('shield');
    }
}
