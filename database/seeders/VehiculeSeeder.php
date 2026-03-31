<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicule;

class VehiculeSeeder extends Seeder
{
    public function run(): void
    {
        Vehicule::create([
            'immatr' => 'AB-1234-RB',
            'marque' => 'Toyota',
            'date_mise_circulation' => '2022-01-01',
            'statut' => '1',
            'disponibilite' => 'DISPONIBLE',
            'capacite' => 5,
            'type_vehicule_id' => 1,
        ]);
    }
}