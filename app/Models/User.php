<?php

namespace App\Models;

use App\Models\CategorieUser;
use App\Models\Chauffeur;
use App\Models\DemandeVehicule;
use App\Models\Direction;
use App\Models\Entite;
use App\Models\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'tel',
        'statut',
        'role_id',
        'categorie_user_id',
        'entite_id', // Clé étrangère vers la table entites
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Validation rules (optionnelles selon ton usage)
     */
    public static $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    //  Lien vers l'entité (service, département, etc.)
    public function entite()
    {
        return $this->belongsTo(Entite::class, 'entite_id');
    }

    public function directon(){
        return $this->belongsTo(Direction::class, 'direction_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function categorieUser()
    {
        return $this->belongsTo(CategorieUser::class, 'categorie_user_id');
    }

    public function demandeVehicules()
    {
        return $this->hasMany(DemandeVehicule::class, 'user_id');
    } 

     public function cheuffeur(){
        return $this->hasOne(Chauffeur::class, 'user_id');
    }
}
