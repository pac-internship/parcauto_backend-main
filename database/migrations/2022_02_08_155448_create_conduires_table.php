<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConduiresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conduires', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('categorie_permis_id');
            $table->unsignedInteger('vehicule_id');
            $table->unique(["categorie_permis_id", "vehicule_id"], 'categorie_vehicule_unique');
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
        Schema::dropIfExists('conduire');
    }
}
