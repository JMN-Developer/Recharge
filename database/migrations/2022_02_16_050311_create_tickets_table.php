<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reseller_id')->unsigned();
            $table->string('ticket_no');
            $table->string('problem_description',1000);
            $table->string('problem_document');
            $table->string('admin_message',1000)->nullable();
            $table->string('status')->default('pending');
            $table->integer('admin_notification')->default(0);
            $table->integer('reseller_notification')->default(0);
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
        Schema::dropIfExists('tickets');
    }
}
