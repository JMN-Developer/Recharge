<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageReadStatusToTicketResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_responses', function (Blueprint $table) {
            //
            $table->boolean('message_read_status_admin')->default(0);
            $table->boolean('message_read_status_user')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_responses', function (Blueprint $table) {
            //
        });
    }
}
