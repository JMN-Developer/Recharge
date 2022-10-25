<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubDataToDueControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('due_controls', function (Blueprint $table) {
            //
            $table->string('reseller_type')->default('user');
            $table->integer('reseller_parent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('due_controls', function (Blueprint $table) {
            //
            $table->dropColumn(['reseller_type','reseller_parent']);
        });
    }
}
