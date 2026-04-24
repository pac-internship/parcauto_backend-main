<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\DemandeVehicule;
use App\Models\AffectationDemande;
use App\Models\TypeVehicule;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Récupérer les statistiques complètes du dashboard global
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            
            $totalVehicules = Vehicule::count();
            $vehiculesDisponibles = Vehicule::where('disponibilite', 'Disponible')->count();
            $totalChauffeurs = Chauffeur::count();
            $chauffeursActifs = Chauffeur::where('statut', 1)->count();
            $totalUtilisateurs = User::count();
            $utilisateursActifs = User::where('statut', 1)->count();

    
            $totalDemandes = DemandeVehicule::count();
            $demandesEnCours = DemandeVehicule::whereIn('statut', ['DEMARREE', 'AFFECTEE'])->count();
            $demandesTerminees = DemandeVehicule::where('statut', 'TERMINEE')->count();
            $demandesCreees = DemandeVehicule::where('statut', 'CREEE')->count();
            $demandesAffectees = DemandeVehicule::where('statut', 'AFFECTEE')->count();


            $today = Carbon::today();
            $demandesApprouveesJour = DemandeVehicule::whereDate('created_at', $today)->where('statut', 'AFFECTEE')->count();
            $rendezvousProgrammes = DemandeVehicule::whereDate('date_depart', '>=', $today)->whereIn('statut', ['CREEE', 'AFFECTEE'])->count();


            $vehiculesEnCourse = Vehicule::where('disponibilite', 'Course')->count();
            $vehiculesEnMaintenance = Vehicule::where('disponibilite', 'Panne')->count();
            $vehiculesIndisponibles = Vehicule::whereIn('disponibilite', ['Panne', 'Course', 'Indisponible'])->count();

       
            $etatVehicules = Vehicule::select('disponibilite', DB::raw('COUNT(*) as count'))
                ->groupBy('disponibilite')
                ->get()
                ->map(fn($item) => [
                    'statut' => $item->disponibilite ?? 'inconnu',
                    'count' => $item->count
                ])
                ->values();

          
            $typeVehicules = TypeVehicule::select('type_vehicules.id', 'type_vehicules.libelle', 'type_vehicules.statut')
                ->selectRaw('COUNT(vehicules.id) as count')
                ->leftJoin('vehicules', 'type_vehicules.id', '=', 'vehicules.type_vehicule_id')
                ->groupBy('type_vehicules.id', 'type_vehicules.libelle', 'type_vehicules.statut')
                ->get()
                ->map(fn($type) => [
                    'id' => $type->id,
                    'libelle' => $type->libelle,
                    'count' => $type->count ?? 0,
                    'statut' => $type->statut
                ])
                ->values();

           
            $tauxUtilisation = $this->calculateUtilisationRate($totalVehicules, $vehiculesDisponibles);

         
            
            $alertesCritiques = $this->getAlertesCritiques($vehiculesEnMaintenance, $totalChauffeurs, $chauffeursActifs);
            $tachesUrgentes = $this->getTachesUrgentes();

           
            
            $derniersTrajets = $this->getDerniersTrajets();
            $activitesRecentes = $this->getActivitesRecentes();

           
            
            $demandesParJour = $this->getDemandesParJour();
            $demandesAssignesParJour = $this->getDemandesAssigneesParJour();
            $demandesTermineesParJour = $this->getDemandesTermineesParJour();

          
            
            $derniersDemandes = $this->getDerniersDemandes();
            $demandesEnCoursListe = $this->getDemandesEnCoursListe();
            $demandesTermineesListe = $this->getDemandesTermineesListe();

            
            
            $sante = $this->calculateSystemHealth($totalVehicules, $chauffeursActifs, $demandesEnCours);

           
            
            return response()->json([
                'total_vehicules' => $totalVehicules,
                'vehicules_disponibles' => $vehiculesDisponibles,
                'total_chauffeurs' => $totalChauffeurs,
                'chauffeurs_actifs' => $chauffeursActifs,
                'total_utilisateurs' => $totalUtilisateurs,
                'utilisateurs_actifs' => $utilisateursActifs,
                'total_demandes' => $totalDemandes,
                'demandes_en_cours' => $demandesEnCours,
                'demandes_affectees' => $demandesAffectees,
                'demandes_creees' => $demandesCreees,
                'demandes_terminees' => $demandesTerminees,
                'demandes_approuvees_jour' => $demandesApprouveesJour,
                'rendez_vous_programmes' => $rendezvousProgrammes,
                'vehicules_maintenance' => $vehiculesEnMaintenance,
                'vehicules_en_course' => $vehiculesEnCourse,
                'vehicules_indisponibles' => $vehiculesIndisponibles,
                'etat_vehicules' => $etatVehicules,
                'type_vehicules' => $typeVehicules,
                'taux_utilisation' => $tauxUtilisation,
                'alertes_critiques' => $alertesCritiques,
                'activites_recentes' => $activitesRecentes,
                'taches_urgentes' => $tachesUrgentes,
                'derniers_trajets' => $derniersTrajets,
                'sante' => $sante,
                'demandes_par_jour' => $demandesParJour,
                'demandes_assigees_par_jour' => $demandesAssignesParJour,
                'demandes_terminees_par_jour' => $demandesTermineesParJour,
                'dernieres_demandes' => $derniersDemandes,
                'demandes_en_cours_liste' => $demandesEnCoursListe,
                'demandes_terminees_liste' => $demandesTermineesListe,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer le taux d'utilisation des véhicules
     */
    private function calculateUtilisationRate($total, $disponibles): float
    {
        if ($total == 0) return 0;
        return round((($total - $disponibles) / $total) * 100, 2);
    }

    /**
     * Récupérer les alertes critiques
     */
    private function getAlertesCritiques($vehiculesEnMaintenance, $totalChauffeurs, $chauffeursActifs): array
    {
        $alertes = [];

        if ($vehiculesEnMaintenance > 0) {
            $alertes[] = [
                'type' => 'warning',
                'titre' => 'Véhicules en panne',
                'message' => "$vehiculesEnMaintenance véhicule(s) en panne",
                'count' => $vehiculesEnMaintenance,
                'priorite' => 'moyenne'
            ];
        }

        $chauffeursIndisponibles = $totalChauffeurs - $chauffeursActifs;
        if ($chauffeursIndisponibles > 0) {
            $alertes[] = [
                'type' => 'warning',
                'titre' => 'Chauffeurs indisponibles',
                'message' => "$chauffeursIndisponibles chauffeur(s) indisponible(s)",
                'count' => $chauffeursIndisponibles,
                'priorite' => 'moyenne'
            ];
        }

        // Demandes non affectées
        $demandesNonAffectees = DemandeVehicule::where('statut', 'CREEE')
            ->whereNull('chauffeur_id')
            ->count();

        if ($demandesNonAffectees > 0) {
            $alertes[] = [
                'type' => 'danger',
                'titre' => 'Demandes en attente',
                'message' => "$demandesNonAffectees demande(s) en attente d'affectation",
                'count' => $demandesNonAffectees,
                'priorite' => 'haute'
            ];
        }

        return $alertes;
    }

    /**
     * Récupérer les tâches urgentes
     */
    private function getTachesUrgentes(): array
    {
        $today = Carbon::today();
        
        $tachesUrgentes = DemandeVehicule::where('statut', '!=', 'TERMINEE')
            ->whereDate('date_depart', '<=', $today)
            ->with(['user', 'chauffeur.user'])
            ->orderBy('date_depart', 'asc')
            ->take(5)
            ->get()
            ->map(fn($demande) => [
                'id' => $demande->id,
                'reference' => $demande->reference ?? 'Demande #' . $demande->id,
                'objet' => $demande->objet,
                'date_depart' => $demande->date_depart ? Carbon::parse($demande->date_depart)->format('d/m/Y H:i') : 'N/A',
                'statut' => $demande->statut,
                'client' => ($demande->user?->nom ?? '') . ' ' . ($demande->user?->prenom ?? ''),
                'chauffeur' => $demande->chauffeur?->user ? ($demande->chauffeur->user->nom . ' ' . $demande->chauffeur->user->prenom) : 'Non assigné',
                'urgence' => 'haute'
            ])
            ->values();

        return $tachesUrgentes->toArray();
    }

    /**
     * Récupérer les derniers trajets
     */
    private function getDerniersTrajets(): array
    {
        $derniersTrajets = AffectationDemande::with(['chauffeur.user', 'vehicule', 'demandeCourse'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($affectation) => [
                'id' => $affectation->id,
                'demande_id' => $affectation->demande_vehicule_id,
                'reference' => $affectation->demandeCourse?->reference ?? 'Demande #' . $affectation->demande_vehicule_id,
                'date' => $affectation->demandeCourse?->date_depart ? Carbon::parse($affectation->demandeCourse->date_depart)->format('d/m/Y H:i') : $affectation->created_at->format('d/m/Y H:i'),
                'chauffeur' => $affectation->chauffeur?->user ? ($affectation->chauffeur->user->nom . ' ' . $affectation->chauffeur->user->prenom) : 'N/A',
                'vehicule' => $affectation->vehicule?->immatr ?? 'N/A',
                'depart' => $affectation->demandeCourse?->point_depart ?? 'N/A',
                'destination' => $affectation->demandeCourse?->point_destination ?? 'N/A',
            ])
            ->values();

        return $derniersTrajets->toArray();
    }

    /**
     * Récupérer les activités récentes
     */
    private function getActivitesRecentes(): array
    {
        $activites = DemandeVehicule::with(['user', 'chauffeur.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($demande) => [
                'id' => $demande->id,
                'reference' => $demande->reference ?? 'Demande #' . $demande->id,
                'type' => 'demande_vehicule',
                'objet' => $demande->objet,
                'statut' => $demande->statut,
                'date' => $demande->created_at->format('d/m/Y H:i'),
                'utilisateur' => ($demande->user?->nom ?? '') . ' ' . ($demande->user?->prenom ?? ''),
                'description' => "Demande: " . $demande->objet,
            ])
            ->values();

        return $activites->toArray();
    }

    /**
     * Récupérer les demandes groupées par jour de la semaine (cette semaine)
     */
    private function getDemandesParJour(): array
    {
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $data = array_fill(0, 7, 0);

        $demandesCetteSemaine = DemandeVehicule::whereBetween('created_at', [
            Carbon::now()->startOfWeek(Carbon::MONDAY),
            Carbon::now()->endOfWeek(Carbon::SUNDAY)
        ])->get();

        foreach ($demandesCetteSemaine as $demande) {
            $jour = $demande->created_at->dayOfWeek - 1; // 0 = Lundi
            if ($jour >= 0 && $jour < 7) {
                $data[$jour]++;
            }
        }

        return [
            'labels' => $jours,
            'data' => array_values($data)
        ];
    }

    /**
     * Récupérer les demandes affectées groupées par jour (cette semaine)
     */
    private function getDemandesAssigneesParJour(): array
    {
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $data = array_fill(0, 7, 0);

        $affectationsCetteSemaine = AffectationDemande::whereBetween('created_at', [
            Carbon::now()->startOfWeek(Carbon::MONDAY),
            Carbon::now()->endOfWeek(Carbon::SUNDAY)
        ])->get();

        foreach ($affectationsCetteSemaine as $affectation) {
            $jour = $affectation->created_at->dayOfWeek - 1;
            if ($jour >= 0 && $jour < 7) {
                $data[$jour]++;
            }
        }

        return [
            'labels' => $jours,
            'data' => array_values($data)
        ];
    }

    /**
     * Récupérer les demandes terminées groupées par jour (cette semaine)
     */
    private function getDemandesTermineesParJour(): array
    {
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $data = array_fill(0, 7, 0);

        $demandesTermineesCetteSemaine = DemandeVehicule::where('statut', 'TERMINEE')
            ->whereBetween('updated_at', [
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->endOfWeek(Carbon::SUNDAY)
            ])->get();

        foreach ($demandesTermineesCetteSemaine as $demande) {
            $jour = $demande->updated_at->dayOfWeek - 1;
            if ($jour >= 0 && $jour < 7) {
                $data[$jour]++;
            }
        }

        return [
            'labels' => $jours,
            'data' => array_values($data)
        ];
    }

    /**
     * Récupérer les 10 dernières demandes
     */
    private function getDerniersDemandes(): array
    {
        $derniersDemandes = DemandeVehicule::with(['user', 'chauffeur.user', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($demande) => [
                'id' => $demande->id,
                'reference' => $demande->reference ?? 'Demande #' . $demande->id,
                'objet' => $demande->objet,
                'statut' => $demande->statut,
                'date' => $demande->created_at->format('d/m/Y H:i'),
                'user' =>   ($demande->user?->nom ?? '') . ' ' . ($demande->user?->prenom ?? ''),
                'beneficiaire' => ($demande->beneficiaire?->nom ?? null) . ' ' . ($demande->beneficiaire?->prenom ?? null),
                'benf_or_user' => isset($demande->beneficiaire) ? 'benf' : 'user',
                'chauffeur' => $demande->chauffeur?->user ? ($demande->chauffeur->user->nom . ' ' . $demande->chauffeur->user->prenom) : 'Non assigné',
                'vehicule' => $demande->vehicule?->immatr ?? 'N/A',
                'depart' => $demande->point_depart ?? 'N/A',
                'destination' => $demande->point_destination ?? 'N/A',
            ])
            ->values();

        return $derniersDemandes->toArray();
    }

    /**
     * Récupérer les demandes en cours avec détails
     */
    private function getDemandesEnCoursListe(): array
    {
        $demandesEnCours = DemandeVehicule::whereIn('statut', ['DEMARREE', 'AFFECTEE'])
            ->with(['user', 'chauffeur.user', 'vehicule'])
            ->orderBy('date_depart', 'asc')
            ->take(20)
            ->get()
            ->map(fn($demande) => [
                'id' => $demande->id,
                'reference' => $demande->reference ?? 'Demande #' . $demande->id,
                'date' => $demande->date_depart ? Carbon::parse($demande->date_depart)->format('d/m/Y H:i') : 'N/A',
                'user' => ($demande->user?->nom ?? '') . ' ' . ($demande->user?->prenom ?? ''),
                'beneficiaire' => ($demande->beneficiaire?->nom ?? null) . ' ' . ($demande->beneficiaire?->prenom ?? null),
                'benf_or_user' => isset($demande->beneficiaire) ? 'benf' : 'user',
                'depart' => $demande->point_depart ?? 'N/A',
                'destination' => $demande->point_destination ?? 'N/A',
                'chauffeur' => $demande->chauffeur?->user ? ($demande->chauffeur->user->nom . ' ' . $demande->chauffeur->user->prenom) : 'Non assigné',
                'vehicule' => $demande->vehicule?->immatr ?? 'N/A',
                'statut' => $demande->statut,
            ])
            ->values();

        return $demandesEnCours->toArray();
    }

    /**
     * Récupérer les demandes terminées avec notes
     */
    private function getDemandesTermineesListe(): array
    {
        $demandesTerminees = DemandeVehicule::where('statut', 'TERMINEE')
            ->with(['user', 'chauffeur.user', 'vehicule', 'beneficiaire'])
            ->orderBy('date_retour_effectif', 'desc')
            ->take(20)
            ->get()
            ->map(fn($demande) => [
                'id' => $demande->id,
                'reference' => $demande->reference ?? 'Demande #' . $demande->id,
                'date' => $demande->date_retour_effectif ? Carbon::parse($demande->date_retour_effectif)->format('d/m/Y H:i') : 'N/A',
                'user' =>   ($demande->user?->nom ?? '') . ' ' . ($demande->user?->prenom ?? ''),
                'beneficiaire' => ($demande->beneficiaire?->nom ?? '') . ' ' . ($demande->beneficiaire?->prenom ?? ''),
                'benf_or_user' => isset($demande->beneficiaire) ? 'benf' : 'user',
                'depart' => $demande->point_depart ?? 'N/A',
                'destination' => $demande->point_destination ?? 'N/A',
                'chauffeur' => $demande->chauffeur?->user ? ($demande->chauffeur->user->nom . ' ' . $demande->chauffeur->user->prenom) : 'N/A',
                'vehicule' => $demande->vehicule?->immatr ?? 'N/A',
                'note' => $demande->is_note ? rand(3, 5) : null,
            ])
            ->values();

        return $demandesTerminees->toArray();
    }

    /**
     * Calculer la santé du système
     */
    private function calculateSystemHealth($totalVehicules, $chauffeursActifs, $demandesEnCours): string
    {
        if ($totalVehicules == 0) return 'ok';
        
        $ratio = $chauffeursActifs / $totalVehicules;

        if ($ratio >= 0.8 && $demandesEnCours < 10) {
            return 'ok';
        } elseif ($ratio >= 0.5) {
            return 'warning';
        } else {
            return 'critical';
        }
    }
}



 