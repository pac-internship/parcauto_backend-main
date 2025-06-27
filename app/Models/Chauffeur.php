<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'num_permis',
        'adresse',
        'annee_permis',
        'contact',
        'email',
        'statut',
        'disponibilite',
        'categorie_permis_id',
        'user_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id', 'id');
    }

    public function permis(){
        return $this->belongsTo(CategoriePermis::class, 'categorie_permis_id', 'id');
    }

    public function planningGardes(){
        return $this->belongsToMany(Chauffeur::class, 'programmation', 'chauffeur_id', 'planning_garde_id');
    }

    public function affectations() {
        return $this->hasMany(AffectationDemande::class, "chauffeur_id", "id");
    }

    public function last_affectation() {
        return $this->hasOne(AffectationDemande::class, "chauffeur_id", "id")->latest();
    }

    public function occupations() {
        return $this->hasMany(Occupation::class, "chauffeur_id", "id");
    }

}
