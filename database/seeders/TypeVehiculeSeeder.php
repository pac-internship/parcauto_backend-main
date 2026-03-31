<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeVehicule;

class TypeVehiculeSeeder extends Seeder
{
    public function run(): void
    {
        TypeVehicule::create(['libelle' => 'Berline', 'statut' => 'ACTIF']);
        TypeVehicule::create(['libelle' => '4x4', 'statut' => 'ACTIF']);
        TypeVehicule::create(['libelle' => 'Minibus', 'statut' => 'ACTIF']);
    }
}