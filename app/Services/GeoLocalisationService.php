<?php

namespace App\Services;

use App\Models\GeoLocalisation;

class GeoLocalisationService
{
    public function start($rideId, $latitude, $longitude)
    {
        return GeoLocalisation::create([
            'demande_vehicule_id' => $rideId,
            'start_latitude' => $latitude,
            'start_longitude' => $longitude,
            'started_at' => now()
        ]);
    }

    public function end($rideId, $latitude, $longitude){
        $geo = GeoLocalisation::where('demande_vehicule_id', $rideId)->latest()->first();

        if ($geo) {
            $geo->update([
                'end_latitude' => $latitude,
                'end_longitude' => $longitude,
                'ended_at' => now()
            ]);
        }

        return $geo;
    }
}