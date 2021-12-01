<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionIdReloadlyToRechargeHistories extends Migration
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
            $table->string('transaction_id_reloadly')->nullable();
            $table->string('country_code')->nullable();
            $table->string('discount')->nullable();
            $table->string('deliveredAmount')->nullable();
            $table->string('deliveredAmountCurrencyCode')->nullable();
            $table->string('company_name')->nullable();

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
