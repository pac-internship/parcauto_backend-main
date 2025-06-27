<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_depart');
            $table->dateTime('date_retour');

            $table->unsignedInteger('vehicule_id');
            $table->unsignedInteger('demande_vehicule_id');
            $table->unsignedInteger('chauffeur_id');

            $table->foreign('vehicule_id')->on('vehicules')->references('id');
            $table->foreign('demande_vehicule_id')->on('demande_vehicules')->references('id');
            $table->foreign('chauffeur_id')->on('chauffeurs')->references('id');

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
        Schema::dropIfExists('occupations');
    }
}
