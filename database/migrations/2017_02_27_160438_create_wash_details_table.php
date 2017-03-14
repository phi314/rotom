<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWashDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wash_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wash_id')->unisgned();
            $table->integer('item_id')->unsigned();
            $table->smallInteger('qty');
            $table->integer('price');
            $table->integer('subtotal');
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('wash_details');
    }
}
