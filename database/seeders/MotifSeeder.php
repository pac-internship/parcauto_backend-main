<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motif;

class MotifSeeder extends Seeder
{
    public function run(): void
    {
        $motifs = [
            'Mission administrative',
            'Déplacement officiel',
            'Réunion',
            'Formation',
            'Inspection terrain',
            'Transport de matériel',
        ];

        foreach ($motifs as $libelle) {
            Motif::firstOrCreate(
                ['libelle' => $libelle], // condition unique
                ['statut' => 'ACTIF']
            );
        }
    }
}