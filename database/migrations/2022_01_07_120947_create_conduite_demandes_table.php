<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConduiteDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conduite_demandes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('categorie_permis_id');
            $table->unsignedInteger('vehicule_id');
            $table->timestamps();

            $table->foreign('categorie_permis_id')->on('categorie_permis')->references('id');
            $table->foreign('vehicule_id')->on('vehicules')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conduite_demandes');
    }
}
