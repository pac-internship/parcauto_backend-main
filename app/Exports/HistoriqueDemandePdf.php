<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DemandeVehicule;

class HistoriqueDemandePdf extends Controller
{
    protected $debut;
    protected $fin;
    protected $point_destination;
    protected $vehicule_id;
    protected $chauffeur_id;

    public function __construct($debut, $fin, $point_destination, $vehicule_id, $chauffeur_id)
    {
        $this->debut = $debut;
        $this->fin = $fin;
        $this->point_destination = $point_destination;
        $this->vehicule_id = $vehicule_id;
        $this->chauffeur_id = $chauffeur_id;
    }

    /**
     * Récupère et formate les données pour le PDF
     * @return array
     */
    public function getData(): array
    {
        $debut = $this->debut;
        $fin = $this->fin;
        $debut = Carbon::parse($debut)->startOfDay();
        $fin = Carbon::parse($fin)->endOfDay();

        $query = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($q) {
            $q->with('entite');
        }, 'chauffeur' => function ($q) {
            $q->with('user');
        }])->whereBetween('created_at', [$debut, $fin]);

        if (!empty($this->vehicule_id)) {
            $query->where('vehicule_id', $this->vehicule_id);
        }

        if (!empty($this->chauffeur_id)) {
            $query->where('chauffeur_id', $this->chauffeur_id);
        }

        if (!empty($this->point_destination)) {
            $query->where('point_destination', 'like', '%' . $this->point_destination . '%');
        }

        return $query->get()->toArray();
    }

    /**
     * Formate les données pour l'affichage HTML
     * @return array
     */
    public function getFormattedData(): array
    {
        $demandes = $this->getData();
        $formatted = [];

        foreach ($demandes as $demande) {
            $chauffeur = 'Non affectée';
            $vehicule = 'Non affectée';

            if ($demande['statut'] !== env('STATUT_DEMANDE_COURSE_CREEE')) {
                $chauffeur = ($demande['chauffeur']['user']['nom'] ?? '') . ' ' . ($demande['chauffeur']['user']['prenom'] ?? '');
                $vehicule = $demande['vehicule']['immatr'] ?? '';
            }

            $formatted[] = [
                'reference' => $demande['id'],
                'date_demande' => date_format(Carbon::parse($demande['created_at']), 'd/m/Y H:i'),
                'demandeur' => ($demande['user']['nom'] ?? '') . ' ' . ($demande['user']['prenom'] ?? ''),
                'entite' => $demande['user']['entite']['code'] ?? '',
                'chauffeur' => trim($chauffeur),
                'vehicule' => $vehicule,
                'type_vehicule' => $demande['type_vehicule']['libelle'] ?? '',
                'effectif' => $demande['nbre_personnes'],
                'trafic' => $demande['point_depart'] . ' à ' . $demande['point_destination'],
                'escales' => $demande['escales'],
                'objet' => $demande['objet'],
                'date_fin' => date_format(Carbon::parse($demande['date_retour']), 'd/m/Y H:i'),
                'statut' => $demande['statut'],
            ];
        }

        return $formatted;
    }

    /**
     * Retourne les statistiques
     * @return array
     */
    public function getStatistics(): array
    {
        $demandes = $this->getData();
        $demandesNouvelles = collect($demandes)->where('statut', 'CREEE')->count();
        $demandesEncours = collect($demandes)->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
        $demandesTerminees = collect($demandes)->where('statut', 'TERMINEE')->count();

        return [
            'nouvelles' => $demandesNouvelles,
            'encours' => $demandesEncours,
            'terminees' => $demandesTerminees,
            'total' => count($demandes),
        ];
    }
}
