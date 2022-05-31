<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtHululsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mt_hululs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('login');
            $table->string('email');
            $table->string('account_type');
            $table->string('currency');
            $table->string('group');
            $table->string('leverage');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('password');
            $table->string('invest_password');
            $table->string('phone_password')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('color')->nullable();
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
        Schema::dropIfExists('mt_hululs');
    }
}
