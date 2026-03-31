<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriePermis;

class CategoriePermisSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['libelle' => 'A', 'statut' => 'ACTIF'],
            ['libelle' => 'B', 'statut' => 'ACTIF'],
            ['libelle' => 'C', 'statut' => 'ACTIF'],
            ['libelle' => 'D', 'statut' => 'ACTIF'],
        ];

        foreach ($categories as $categorie) {
            CategoriePermis::create($categorie);
        }
    }
}