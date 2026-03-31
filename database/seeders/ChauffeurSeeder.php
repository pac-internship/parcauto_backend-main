<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chauffeur;

class ChauffeurSeeder extends Seeder
{
    public function run(): void
    {
        Chauffeur::create([
            'matricule' => '001',
            'num_permis' => 'PER12345',
            'adresse' => 'Cotonou',
            'annee_permis' => 2018,
            'contact' => '97000000',
            'email' => 'chauffeur1@test.com',
            'statut' => '1',
            'disponibilite' => 'DISPONIBLE',
            'user_id' => 1 ,
            'categorie_permis_id' => 2,
        ]);
    }
}