<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJumpDriveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jump_drive', function (Blueprint $table) {
            $table->bigIncrements('jump_drive_id');
            $table->string("name", 255);
            $table->integer("charge_time");
            $table->integer("range");
            $table->integer("cargo_cost");
            $table->integer("base_price");
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
        Schema::dropIfExists('jump_drive');
    }
}
