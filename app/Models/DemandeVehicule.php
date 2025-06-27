<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeVehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'objet',
        'point_depart',
        'point_destination',
        'nbre_personnes',
        'statut',
        'escales',
        'user_id',
        'beneficiaire_id',
        'motif_id',
        'is_note',
        'type_vehicule_id',
        'date_depart',
        'date_retour',
        'heure_depart',
        'heure_retour',
        'date_depart_effectif',
        'date_retour_effectif',
        'date',
        'vehicule_id',
        'chauffeur_id'
    ];

    public function critereNotation(){
        return $this->belongsToMany(CritereNotation::class, 'notes', 'demande_vehicule_id', 'critere_notation_id');
    }

    public function demandeVehicule(){
        return $this->belongsTo(Motif::class, 'motif_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id')->with('direction');
    }

    public function beneficiaire(){
        return $this->belongsTo(User::class, 'beneficiaire_id', 'id')->with('direction');
    }

    public function typeVehicule(){
        return $this->belongsTo(TypeVehicule::class, 'type_vehicule_id', 'id');
    }

    public function motif(){
        return $this->belongsTo(Motif::class, 'motif_id', 'id');
    }

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }

    public function vehicule(){
        return $this->belongsTo(Vehicule::class, 'vehicule_id', 'id');
    }

    public function affectation(){
        return $this->hasOne(AffectationDemande::class)->with('vehicule','chauffeur','chauffeur.user')->latest();
    }

}
