<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->bigIncrements('message_id');
            $table->integer("recipient_user_id");
            $table->integer("sender_user_id");
            $table->timestamp("time");
            $table->string("subject", 255);
            $table->text("content");
            $table->boolean("received");
            $table->boolean("visible_to_recipient");
            $table->boolean("visible_to_sender");
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
        Schema::dropIfExists('message');
    }
}
