<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            //$table->foreign('User_name')->references('name')->on('users');
            $table->unsignedBigInteger('user_id');
            $table->string('IsActive')->default('Y');
            $table->integer('payment_Amount');
            $table->date('due_date');
            $table->char('payment_status')->default('pending');;
            $table->timestamps();
            

            $table->foreign('user_id')->references('id')->on('users');
          //  $table->unique('user_id');


            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
