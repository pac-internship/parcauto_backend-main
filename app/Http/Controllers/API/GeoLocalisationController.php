<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeoLocalisation;
use App\Services\GeoLocalisationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function saveIntermediaireLocationCource(Request $request, $demande_course_id,){
         try{
            $geo = GeoLocalisation::where('demande_vehicule_id', $demande_course_id)->latest()->first();
            if($geo) {
                $entermediaire_lon_lat = $geo->entermediaire_lon_lat != null ? json_decode($geo->entermediaire_lon_lat, true) : [];
                $entermediaire_lon_lat[] = ['latitude' => $request->latitude, 'longitude' => $request->longitude, 'recorded_at' => now()];
                $geo->entermediaire_lon_lat = json_encode($entermediaire_lon_lat);
                $geo->save();
                return response()->json([
                'message' => 'Coordonnés ajouter avec succes ! ',
                'success' => 'success',
                'status' => 200
            ],200);
            }
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
