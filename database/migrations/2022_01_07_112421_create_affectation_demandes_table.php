<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffectationDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affectation_demandes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicule_id');
            $table->unsignedInteger('demande_vehicule_id');
            $table->unsignedInteger('chauffeur_id');
            $table->timestamps();

            $table->foreign('vehicule_id')->on('vehicules')->references('id');
            $table->foreign('demande_vehicule_id')->on('demande_vehicules')->references('id');
            $table->foreign('chauffeur_id')->on('chauffeurs')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affectation_demandes');
    }
}
