<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeoLocalisation;
use Illuminate\Http\Request;

class GeoLocalisationController extends Controller
{
     
    public function show($rideId){
       try{
        $geo = GeoLocalisation::where('demande_vehicule_id', $rideId)->latest()->first();
        
            return response()->json([
                'geo' => $geo,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }
}
