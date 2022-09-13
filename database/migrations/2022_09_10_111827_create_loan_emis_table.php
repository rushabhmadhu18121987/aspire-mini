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
        Schema::create('loan_emis', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->integer('emi_amount'); //Total Payable EMI Amount (Principal + Intrest )
            $table->integer('emi_intrest')->default(0);//Intreset
            $table->integer('emi_principal');
            $table->integer('emi_due_date');
            $table->integer('status')->default(0); //0 = PENDING
            $table->tinyInteger('is_regular_emi')->default(1); //0 = REGULAR EMI, 1 = ADDITIONAL REPAYMENT
            $table->integer('created_at'); //Current Unix timestamp When EMI Created
            $table->integer('updated_at')->nullable();//Current Unix timestamp To Update When EMI PAID-Up
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_emis');
    }
};
