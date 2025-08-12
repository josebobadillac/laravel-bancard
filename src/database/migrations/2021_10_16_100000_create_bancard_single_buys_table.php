<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancardSingleBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancard_single_buys', function (Blueprint $table) {
            $table->text('id');
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('additional_data', 100)->nullable();
            $table->string('description', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('process_id', 36)->nullable();
            $table->boolean('zimple')->default(false);
            $table->boolean('pre_authorization')->default(false);
            $table->timestamps();
        });

        //Case your conection name is diferent, you need configurate this. o adjust if is necesary
        if(env('DB_CONNECTION') == 'mysql') {
            DB::statement('ALTER Table bancard_single_buys ADD shop_process_id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');
        } 

        if(env('DB_CONNECTION') == 'pgsql') {
            DB::statement('ALTER TABLE bancard_single_buys ADD shop_process_id SERIAL NOT NULL UNIQUE;');
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancard_single_buys');
    }
}
