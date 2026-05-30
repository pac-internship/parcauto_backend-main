<?php

namespace App\Models;

use App\Models\DemandeVehicule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoLocalisation extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_vehicule_id', 
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'started_at',
        'ended_at',
        'entermediaire_lon_lat',
        'km_total'
    ];

    public function demandeVehicule()
    {
        return $this->belongsTo(DemandeVehicule::class);
    }
    
}
