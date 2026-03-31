<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entite;

class EntiteSeeder extends Seeder
{
    public function run(): void
    {
        $direction = Entite::create([
            'nom' => 'Direction Générale',
            'code' => 'DG',
            'type' => 'DIRECTION',
            'parent_id' => null,
        ]);

        Entite::create([
            'nom' => 'Service Logistique',
            'code' => 'SL',
            'type' => 'SERVICE',
            'parent_id' => $direction->id,
        ]);
    }
}