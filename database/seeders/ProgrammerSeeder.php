<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Programmer;
use App\Models\Chauffeur;
use App\Models\PlanningGarde;
use Carbon\Carbon;

class ProgrammerSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier que les données nécessaires existent
        if (Chauffeur::count() == 0 || PlanningGarde::count() == 0) {
            $this->command->warn('Chauffeurs ou PlanningGardes manquants. Seeder annulé.');
            return;
        }

        $chauffeurs = Chauffeur::pluck('id');
        $plannings = PlanningGarde::pluck('id');

        foreach ($plannings as $planningId) {
            // Associer 2 chauffeurs aléatoires à chaque planning
            $randomChauffeurs = $chauffeurs->random(min(2, $chauffeurs->count()));

            foreach ((array) $randomChauffeurs as $chauffeurId) {
                Programmer::create([
                    'chauffeur_id' => $chauffeurId,
                    'planning_garde_id' => $planningId,
                    'date_fin_repos' => Carbon::now()->addDays(rand(1, 5)),
                ]);
            }
        }
    }
}