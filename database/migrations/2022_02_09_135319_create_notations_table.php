<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('commentaire')->nullable();
            $table->unsignedInteger('demande_vehicule_id');
            $table->timestamps();
            $table->unsignedInteger('user_id');
            $table->dateTime('date_de_notation');


            $table->foreign('demande_vehicule_id')->on('demande_vehicules')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notations');
    }
}
