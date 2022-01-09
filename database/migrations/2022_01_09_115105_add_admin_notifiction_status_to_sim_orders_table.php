<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminNotifictionStatusToSimOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sim_orders', function (Blueprint $table) {
            //
            $table->integer('admin_notification')->default(0);
            $table->integer('reseller_notification')->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sim_orders', function (Blueprint $table) {
            //
        });
    }
}
