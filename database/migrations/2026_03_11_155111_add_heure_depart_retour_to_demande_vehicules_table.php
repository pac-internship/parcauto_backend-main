<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('demande_vehicules', function (Blueprint $table) {
            $table->time('heure_depart')->nullable()->after('date_depart');
            $table->time('heure_retour')->nullable()->after('date_retour');
        });
    }

    public function down(): void
    {
        Schema::table('demande_vehicules', function (Blueprint $table) {
            $table->dropColumn(['heure_depart', 'heure_retour']);
        });
    }
};