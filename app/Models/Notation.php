<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notation extends Model
{
    use HasFactory;
    protected $fillable=[
        'demande_vehicule_id',
        'commentaire',
        'date_de_notation',
        'user_id',
    ];
}
