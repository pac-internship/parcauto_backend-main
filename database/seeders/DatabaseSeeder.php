<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
{
    $this->call([
        RoleSeeder::class,
        CategorieUserSeeder::class,
        EntiteSeeder::class,
        UserSeeder::class,
        CategoriePermisSeeder::class,
        TypeVehiculeSeeder::class,
        ChauffeurSeeder::class,
        MotifSeeder::class,
        VehiculeSeeder::class,
        DemandeVehiculeSeeder::class,
        PlanningGardeSeeder::class,
        OccupationSeeder::class,
        ProgrammerSeeder::class,
        JournalSmsSeeder::class,
    ]);
}
}
