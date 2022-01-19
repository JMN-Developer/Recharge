<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceBeforeRechargeToRechargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recharge_histories', function (Blueprint $table) {
            //
            $table->float('balance_before_recharge')->default(0);
            $table->float('balance_after_recharge')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recharge_histories', function (Blueprint $table) {
            //
        });
    }
}
