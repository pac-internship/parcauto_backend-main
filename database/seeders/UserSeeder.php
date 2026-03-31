<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
public function run()
{
    User::create([
        'nom' => 'AHO',
        'prenom' => 'Krizostome',
        'email' => 'admin@example.com',
        'tel' => '0102030405',
        'statut' => 1,
        'password' => Hash::make('password123'),
        'role_id' => 1,
        'categorie_user_id' => 1,
        'entite_id' => 1,
    ]);
}
}
