<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeVehiculesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_vehicules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->nullable();
            $table->string('objet');
            $table->dateTime('date_depart');
            $table->dateTime('date_retour');
            $table->dateTime('date_depart_effectif')->nullable();
            $table->dateTime('date_retour_effectif')->nullable();
            $table->string('point_depart');
            $table->string('point_destination');
            $table->integer('nbre_personnes');
            $table->string('statut');
            $table->boolean('is_note')->default(false);
            $table->string('escales')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('motif_id');
            $table->unsignedInteger('type_vehicule_id');
            $table->unsignedInteger('chauffeur_id');
            $table->unsignedInteger('vehicule_id');

            $table->foreign('type_vehicule_id')->on('type_vehicules')->references('id');
            $table->foreign('motif_id')->on('motifs')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('chauffeur_id')->on('chauffeurs')->references('id');
            $table->foreign('vehicule_id')->on('vehicules')->references('id');
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
        Schema::dropIfExists('demande_vehicules');
    }
}
