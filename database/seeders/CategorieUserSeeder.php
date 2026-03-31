<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategorieUser;

class CategorieUserSeeder extends Seeder
{

public function run()
{
    CategorieUser::create([
        'libelle' => 'AGENT',
        'statut' => 'ACTIF'
    ]);

    CategorieUser::create([
        'libelle' => 'INVITE',
        'statut' => 'ACTIF'
    ]);
}
}
