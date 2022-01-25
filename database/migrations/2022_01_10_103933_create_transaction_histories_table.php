<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('reseller_id');
            $table->string('transaction_id');
            $table->integer('transaction_source_id');
            $table->string('transaction_type');
            $table->string('transaction_source');
            $table->float('amount');
            $table->string('transaction_wallet');
            $table->string('wallet_type');
            $table->string('wallet_before_transaction');
            $table->string('wallet_after_transaction');
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
        Schema::dropIfExists('transaction_histories');
    }
}
