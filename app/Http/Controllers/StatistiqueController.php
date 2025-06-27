<?php

namespace App\Http\Controllers;

use App\Models\DemandeVehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class StatistiqueController extends Controller
{
    //

    public function getDemandesStatistiqueByRole($user_id,$role){
        try {
            $demandesCrees = 0;
            $demandesTerminees = 0;
            $demandesEncours = 0;
            $demandesTotales = 0;
            if($role == env('ROLE_ADMIN')){
                $demandesEncours = DemandeVehicule::whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesCrees = DemandeVehicule::where('statut', 'CREEE')->count();
                $demandesTerminees = DemandeVehicule::where('statut', 'TERMINEE')->count();
                $demandesTotales = DemandeVehicule::count();
            }
            else{
                $demandesEncours = DemandeVehicule::where('user_id',$user_id)
                ->where('statut', 'CREEE')->count();
                $demandesCrees = DemandeVehicule::where('user_id',$user_id)
                ->where('statut', 'CREEE')->count();
                $demandesTerminees = DemandeVehicule::where('user_id',$user_id)
                ->where('statut', 'CREEE')->count();
                $demandesTotales = DemandeVehicule::where('user_id',$user_id)
                ->count();
            }

            return response()->json([
                'demandesTotales' => $demandesTotales,
                'demandesCrees' => $demandesCrees,
                'demandesEncours' => $demandesEncours,
                'demandesTerminees' => $demandesTerminees,
                'message' => '',
                'status' => 200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }

    }

}
