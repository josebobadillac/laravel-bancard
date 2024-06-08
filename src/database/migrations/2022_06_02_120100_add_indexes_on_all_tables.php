<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesOnAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bancard_single_buys', function (Blueprint $table) {
            $table->index('process_id');
            $table->index('created_at');
        });

        Schema::table('bancard_confirmations', function (Blueprint $table) {
            $table->index('shop_process_id');
            $table->index('authorization_number');
            $table->index('ticket_number');
            $table->index('created_at');
        });

        Schema::table('bancard_rollbacks', function (Blueprint $table) {
            $table->index('shop_process_id');
            $table->index('created_at');
        });

        Schema::table('bancard_user_cards', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bancard_single_buys', function (Blueprint $table) {
            $table->dropIndex('process_id');
            $table->dropIndex('created_at');
        });

        Schema::table('bancard_confirmations', function (Blueprint $table) {
            $table->dropIndex('shop_process_id');
            $table->dropIndex('authorization_number');
            $table->dropIndex('ticket_number');
            $table->dropIndex('created_at');
        });

        Schema::table('bancard_rollbacks', function (Blueprint $table) {
            $table->dropIndex('shop_process_id');
            $table->dropIndex('created_at');
        });

        Schema::table('bancard_user_cards', function (Blueprint $table) {
            $table->dropIndex('user_id');
            $table->dropIndex('created_at');
        });
    }
}
