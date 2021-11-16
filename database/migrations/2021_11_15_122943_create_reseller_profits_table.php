<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_profits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reseller_id')->unsigned();
            $table->string('international_recharge_profit')->default(0);
            $table->string('domestic_recharge_profit')->default(0);
            $table->timestamps();
            $table->foreign('reseller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reseller_profits');
    }
}
