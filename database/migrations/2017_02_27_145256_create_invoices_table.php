<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wash_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('downpayment');
            $table->integer('debt');
            $table->integer('total_price');
            $table->string('notes')->nullable();
            $table->boolean('settled')->default(false);
            $table->integer('admin_user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('wash_id')->references('id')->on('washes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('admin_user_id')->references('id')->on('admin_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
