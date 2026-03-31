<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\Role;


class RoleSeeder extends Seeder
{
  
public function run()
{
   Role::create([
        'libelle' => 'ADMIN',
        'statut' => 'ACTIF'
    ]);

    Role::create([
        'libelle' => 'UTILISATEUR',
        'statut' => 'ACTIF'
    ]);
}
}
