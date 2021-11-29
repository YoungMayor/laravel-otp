<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneTimePinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_time_pins', function (Blueprint $table) {
            $table->id();
            $table->string('pin', 56);
            $table->string('action', 256);
            $table->string('email', 128);
            $table->longText('payload')->nullable();
            $table->string('source_type', 256)->nullable();
            $table->foreignId('source_id')->nullable();
            $table->timestamp('expires_at');
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
        Schema::dropIfExists('one_time_pins');
    }
}
