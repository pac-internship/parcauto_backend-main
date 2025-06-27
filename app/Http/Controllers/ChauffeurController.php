<?php

namespace App\Http\Controllers;
use App\Models\Chauffeur;
use App\Models\CategoriePermis;
use App\Models\Conduire;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    ///
    public function getChauffeurs(){

        try{
            $data = Chauffeur::with('user')->with('permis')->where('statut', true)->get();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }
    //Liste des Agents
    public function getAgents(){

        try{            
            
            /*$data = User::whereNotIn('id', function($query) {
                $query->select('user_id')->from('chauffeurs')->where('statut', true);
            })->get();*/
            $data = User::where('statut', true)->get();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    public function checkExistingChauffeur($id){
        try{
            $data = Chauffeur::where('user_id', $id)->first();
            return $data;

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    public function saveChauffeur(Request $request){
        try{
            $input = $request->input('body');

            $chauffeur = $this->checkExistingChauffeur($input['user_id']);
            if($chauffeur){ // Update
                $chauffeur->update($input);
                return response()->json([
                    'success' => 'success',
                    'message' => 'Le chauffeur a été mise à jour avec succès.',
                    'status' => 200
                ]);
            }
            Chauffeur::create($input);
            return response()->json([
                'success' => 'success',
                'message' => 'Le chauffeur a été ajouté avec succès.',
                'status' => 200
            ]);
        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    //Types véhicules methodes
    public function getTypesChauffeurs(){
        try{
            $data = TypeChauffeur::where('statut', true)->get();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }
    
    public function checkExistingTypeChauffeur($libelle){
        try{
            $data = TypeChauffeur::where('libelle', $libelle)->first();
            return $data;

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }
    
    public function getChauffeurById($id){
        try{
            $data = Chauffeur::with('user')->with('permis')->where('id', $id)->first();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

}
