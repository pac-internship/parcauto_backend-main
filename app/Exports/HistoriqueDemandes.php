<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Carbon\Carbon;
use App\Models\DemandeVehicule;

class HistoriqueDemandes extends Controller implements FromArray 
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


    public function array(): array{
        
        $debut = $this->debut;
        $fin = $this->fin;
        $debut = Carbon::parse($debut)->startOfDay();
        $fin = Carbon::parse($fin)->endOfDay();

        $query = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($q) {
            $q->with('entite');
        }, 'chauffeur' => function ($q) {
            $q->with('user');
        }])->whereBetween('created_at', [$debut, $fin]);

        
        if(!empty($this->vehicule_id)){
            $query->where('vehicule_id', $this->vehicule_id);
        }
            
        if(!empty($this->chauffeur_id)){
            $query->where('chauffeur_id', $this->chauffeur_id);
        }
            
        if(!empty($this->point_destination)){
            $query->where('point_destination', 'like', '%'.$this->point_destination.'%');
        }
            
        $demandes = $query->get();
        
        $journal_array[] = array(
            'Référence',
            'Date Demande', 
            'Demandeur', 
            'Entite', 
            'Chauffeur',
            'Vehicule', 
            'Type Véhicule', 
            'Effectif', 
            'Trafic',
            'Escales',
            'Objet',
            'Date Fin',
            'Statut',
        );
        
            foreach($demandes as $demande)
            {
                if($demande->statut == env('STATUT_DEMANDE_COURSE_CREEE')){
                    $journal_array[] = array(
                        'Reference' => $demande->id,
                        'Date Demande' => $demande->created_at,  
                        'Demandeur' => $demande->user->nom.' '.$demande->user->prenom, 
                        'Entite' => $demande->user->entite->code,
                        'Chauffeur' => 'Non affectée',
                        'Vehicule' => 'Non affectée',
                        'Type Véhicule'=>$demande->typeVehicule->libelle, 
                        'Effectif'=> $demande->nbre_personnes, 
                        'Trafic' => $demande->point_depart.' à '.$demande->point_destination,
                        'Escales' => $demande->escales,
                        'Objet' => $demande->objet, 
                        'Date Fin' => $demande->date_retour. ' '.$demande->heure_retour , 
                        'Statut' => $demande->statut, 
                    );
                } else {
                    $journal_array[] = array(
                        'Reference' => $demande->id,
                        'Date Demande' => $demande->date,  
                        'Demandeur' => $demande->user->nom.' '.$demande->user->prenom, 
                        'Entite' => $demande->user->entite->code,
                        'Chauffeur' => $demande->chauffeur?->user->nom.' '.$demande->chauffeur?->user->prenom,
                        'Vehicule' =>  $demande->vehicule?->immatr, 
                        'Type Véhicule'=>$demande->typeVehicule->libelle, 
                        'Effectif'=> $demande->nbre_personnes, 
                        'Trafic' => $demande->point_depart.' à '.$demande->point_destination,
                        'Escales' => $demande->escales,
                        'Objet' => $demande->objet, 
                        'Date Fin' => $demande->date_retour. ' '.$demande->heure_retour , 
                        'Statut' => $demande->statut, 
                    );
                }
                
            }           
        
        Log::info($journal_array);
        
        return $journal_array;
    }
}
