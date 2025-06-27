<?php

namespace App\Models;

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
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'tel',
        'statut',
        'role_id',
        'categorie_user_id',
        'direction_id',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $rules = [
        'email' => 'required',
        'password' => 'required'
    ];

    public function direction(){
        return $this->belongsTo(Direction::class,'direction_id', 'id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id', 'id');
    }

    public function categorieUser(){
        return $this->belongsTo(CategorieUser::class, 'categorie_user_id', 'id');
    }

    public function demandeVehicule(){
        return $this->hasMany(DemandeVehicule::class, 'user_id', 'id');
    }
}
