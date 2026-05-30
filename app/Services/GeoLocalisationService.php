<?php

namespace App\Services;

use App\Models\GeoLocalisation;

class GeoLocalisationService
{
    public function start($rideId, $latitude, $longitude){
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
            $totalDistance = $this->calculateTotalDistance($geo);
            $geo->update([
                'end_latitude' => $latitude,
                'end_longitude' => $longitude,
                'ended_at' => now(),
                'km_total' => $totalDistance,
            ]);
        }

        return $geo;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2){

      $earthRadius = 6371;

      $dLat = deg2rad($lat2 - $lat1);
      $dLon = deg2rad($lon2 - $lon1);

      $a = sin($dLat / 2) * sin($dLat / 2) +
          cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
          sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

      return $earthRadius * $c;
   }

   
   private function calculateTotalDistance($geo){

    $points = json_decode($geo->entermediaire_lon_lat, true) ?? [];

    $allPoints = [];

    $allPoints[] = [
        'lat' => $geo->start_latitude,
        'lng' => $geo->start_longitude
    ];

    foreach ($points as $p) {
        $allPoints[] = [
            'lat' => $p['latitude'],
            'lng' => $p['longitude']
        ];
    }

    $allPoints[] = [
        'lat' => $geo->end_latitude,
        'lng' => $geo->end_longitude
    ];
    $totalDistance = 0;

    for ($i = 0; $i < count($allPoints) - 1; $i++) {
        $totalDistance += $this->calculateDistance(
            $allPoints[$i]['lat'],
            $allPoints[$i]['lng'],
            $allPoints[$i + 1]['lat'],
            $allPoints[$i + 1]['lng']
        );
    }

    return round($totalDistance, 2);
  }
}