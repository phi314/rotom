<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('washes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('user_id')->unsigned();
            $table->integer('admin_user_id')->unsigned();
            $table->integer('washer')->unsigned()->nullable();
            $table->integer('total_kg')->nullable();
            $table->integer('total_pcs')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', array('process', 'finish'));
            $table->dateTime('washed_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::table('washes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('admin_user_id')->references('id')->on('admin_users');
            $table->foreign('washer')->references('id')->on('admin_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('washes');
    }
}
