<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('tel');
            $table->string('statut');
            $table->string('password');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('categorie_user_id');
            $table->unsignedInteger('direction_id');
            $table->timestamps();

            $table->foreign('role_id')->on('roles')->references('id');
            $table->foreign('categorie_user_id')->on('categorie_users')->references('id');
            $table->foreign('direction_id')->on('directions')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
