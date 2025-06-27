<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('chauffeur_id');
            $table->unsignedInteger('planning_garde_id');
            $table->dateTime('date_fin_repos');
            $table->timestamps();

            $table->foreign('chauffeur_id')->on('chauffeurs')->references('id');
            $table->foreign('planning_garde_id')->on('planning_gardes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmation');
    }
}
