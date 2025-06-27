<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLigneNotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ligne_notations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notation_id');
            $table->unsignedInteger('critere_notation_id');
            $table->unsignedInteger('chauffeur_id');
            $table->integer('valeur');
            $table->timestamps();

            $table->foreign('notation_id')->on('notations')->references('id');
            $table->foreign('critere_notation_id')->on('critere_notations')->references('id');
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
        Schema::dropIfExists('ligne_notations');
    }
}
