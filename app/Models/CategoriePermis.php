<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriePermis extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'statut',
    ];

    public function vehicules(){
        return $this->belongsToMany(Vehicule::class, 'conduite_demandes', 'vehicule_id', 'categorie_permis_id');
    }

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class, 'categorie_permis_id', 'id');
    }
}
