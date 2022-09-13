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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->double('amount',10,2); //Float value
            $table->tinyInteger('duration');//In Weeks
            $table->double('total_int_amount',10,2)->default(0.0); //Float value
            $table->tinyInteger('status')->default(0); // 0 = UNAPPROVED
            $table->integer('approved_by')->nullable();//In Case multiple Admin then it can help
            $table->integer('approved_at')->nullable();//In Case multiple Admin then it can help
            $table->integer('created_at'); //Current Unix timestamp
            $table->integer('updated_at')->nullable();//Current Unix timestamp
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
