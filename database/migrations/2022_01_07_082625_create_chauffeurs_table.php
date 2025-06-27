<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChauffeursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matricule');
            $table->string('num_permis');
            $table->string('annee_permis')->nullable();
            $table->string('adresse');
            $table->string('contact');
            $table->string('email');
            $table->boolean('statut');
            $table->enum('disponibilite', [
                env('STATUT_DISPONIBLE'), 
                env('STATUT_INDISPONIBLE'), 
                'REPOS', 'COURSE', 'CONGE', 'ABSENT'
            ]);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('categorie_permis_id');

            // Ajout des colonnes pour les clés étrangères created_by et updated_by
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('categorie_permis_id')->references('id')->on('categorie_permis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chauffeurs');
    }
}
