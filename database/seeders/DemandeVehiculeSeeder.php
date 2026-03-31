<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DemandeVehicule;
use App\Models\User;
use App\Models\Motif;
use App\Models\TypeVehicule;
use App\Models\Chauffeur;
use App\Models\Vehicule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemandeVehiculeSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier que les données nécessaires existent
        if (
            User::count() == 0 ||
            Motif::count() == 0 ||
            TypeVehicule::count() == 0 ||
            Chauffeur::count() == 0 ||
            Vehicule::count() == 0
        ) {
            $this->command->warn('Certaines tables liées sont vides. Seeder annulé.');
            return;
        }

        $users = User::pluck('id');
        $motifs = Motif::pluck('id');
        $types = TypeVehicule::pluck('id');
        $chauffeurs = Chauffeur::pluck('id');
        $vehicules = Vehicule::pluck('id');

        for ($i = 1; $i <= 10; $i++) {

            $dateDepart = Carbon::now()->addDays(rand(1, 10));
            $dateRetour = (clone $dateDepart)->addHours(rand(2, 48));

            DemandeVehicule::create([
                'reference' => 'REF-' . strtoupper(Str::random(6)),
                'objet' => 'Mission administrative ' . $i,
                'date_depart' => $dateDepart,
                'date_retour' => $dateRetour,
                'point_depart' => 'Cotonou',
                'point_destination' => 'Porto-Novo',
                'nbre_personnes' => rand(1, 5),
                'statut' => 'EN_ATTENTE',
                'is_note' => false,
                'escales' => null,
                'user_id' => $users->random(),
                'motif_id' => $motifs->random(),
                'type_vehicule_id' => $types->random(),
                'chauffeur_id' => $chauffeurs->random(),
                'vehicule_id' => $vehicules->random(),
            ]);
        }
    }
}