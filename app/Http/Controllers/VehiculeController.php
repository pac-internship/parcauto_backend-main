<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Vehicule;
use App\Models\TypeVehicule;
use App\Models\CategoriePermis;
use App\Models\Conduire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Unset_;
use Symfony\Component\Console\Input\Input;

class VehiculeController extends Controller
{
    //
    public function getVehicules(){
        try{
            $data = Vehicule::with('type')->where('statut', true)->get();
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

    public function checkExistingVehicule($param){
        try{
            $data = Vehicule::where('id', $param)->orWhere('immatr', $param)->first();
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

    public function saveVehicule(Request $request){
        try{
            $input = $request->all();
            $input['statut'] = 1; // valeur par défaut

            if(!isset($input['id'])){//Création
                try{
                    Vehicule::create($input);
                    return response()->json([
                        'data' => '',
                        'message' => 'Véhicule ajouté avec succès',
                        'status' => 200
                    ],200);
                }catch(Exception $ex){
                    Log::info($ex);
                    return response()->json([
                        'error' => 'error',
                        'message' => 'Une erreur interne est survenue. Veuillez vérifier les champs.',
                        'status' => 500
                    ]);
                }
            }else{//Modification ou suppression
                
                $vehicule = $this->checkExistingVehicule($input['id']);
                if(!$vehicule){ //
                    return response()->json([
                        'error' => 'error',
                        'message' => 'Le vehicule sélectionné n\'existe pas dans la base.',
                        'status' => 500
                    ]);
                }
                $vehicule->update($input);
                return response()->json([
                    'success' => 'success',
                    'message' => 'Le véhicule a bien été mis à jour.',
                    'status' => 200
                ]);
            }

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
    public function getTypesVehicules(){
        try{
            $data = TypeVehicule::where('statut', "ACTIF")->get();
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

   public function saveTypeVehicule(Request $request)
{
    $input = $request->all();
   

    // 🔒 Validation
    if (!isset($input['libelle']) || empty(trim($input['libelle']))) {
        return response()->json([
            'error' => 'Le libellé est obligatoire'
        ], 400);
    }

    // 🔒 Normalisation du statut
    $statut = strtoupper($input['statut'] ?? 'ACTIF');

    if (!in_array($statut, ['ACTIF', 'INACTIF'])) {
        return response()->json([
            'error' => 'Le statut doit être ACTIF ou INACTIF'
        ], 400);
    }

    // 🔍 Vérifier si existe déjà
    $existing = $this->checkExistingTypeVehicule($input['libelle']);

    if ($existing) {
        return response()->json([
            'message' => 'Ce type de véhicule existe déjà'
        ], 409);
    }

    // ✅ Création
    $type_vehicule = TypeVehicule::create([
        'libelle' => trim($input['libelle']),
        'statut' => $statut
    ]);

    return response()->json([
        'message' => 'Type de véhicule créé avec succès',
        'data' => $type_vehicule
    ], 201);
}
    public function checkExistingTypeVehicule($libelle){
        try{
            $data = TypeVehicule::where('libelle', $libelle)->first();
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

    public function getVehiculeById($id){
        try{
            $data = Vehicule::with('conduire')->with('user')->with('type')->where('id', $id)->first();
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

    public function getCategoriePermis(){
        try{
            $data = CategoriePermis::where('statut', 'ACTIF')->get();
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

    public function saveCategoriePermis(Request $request){
        $input = $request->input('body');
        try{
            $categorie_permis = $this->checkExistingCategoriePermis($input['libelle']);
            if($categorie_permis) $categorie_permis->update($input);
            else  CategoriePermis::create($input);
            return response()->json([
                'success' => 'success',
                'message' => 'Enregistrement terminé avec succès.',
                'status' => 200
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue. Veuillez vérifier les champs.',
                'status' => 500
            ]);
        }
    }

    public function saveConduire(Request $request){
        $input = $request->input('body');
        try{
            $conduire = Conduire::where('categorie_permis_id', $input['categorie_permis_id'])
            ->where('vehicule_id', $input['vehicule_id'])->first();
            if(!$conduire)
                Conduire::create($input);
            return response()->json([
                'success' => 'success',
                'message' => 'Enregistrement terminé avec succès.',
                'status' => 200
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue. Veuillez vérifier les champs.',
                'status' => 500
            ]);
        }
    }

    public function deleteConduire(Request $request){
        $input = $request->input('body');
        try{
            $conduire = Conduire::where('categorie_permis_id', $input['categorie_permis_id'])->where('vehicule_id', $input['vehicule_id'])->first();
            $conduire->delete();
            return response()->json([
                'success' => 'success',
                'message' => 'opération terminée avec succès.',
                'status' => 200
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue. Veuillez vérifier les champs.',
                'status' => 500
            ]);
        }
    }
    
    public function checkExistingCategoriePermis($param){
        try{
            $data = CategoriePermis::where('libelle', $param)->first();
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

    public function deleteVehicule($id)
{
    try {
        $vehicule = Vehicule::find($id);

        if (!$vehicule) {
            return response()->json([
                'error' => 'not_found',
                'message' => 'Véhicule non trouvé',
                'status' => 404
            ]);
        }

        // ✅ Suppression logique (soft delete)
        $vehicule->statut = false;
        $vehicule->save();

        return response()->json([
            'success' => 'success',
            'message' => 'Véhicule supprimé avec succès',
            'status' => 200
        ]);

    } catch (\Exception $ex) {
        \Log::error($ex->getMessage());

        return response()->json([
            'error' => 'error',
            'message' => 'Une erreur interne est survenue',
            'status' => 500
        ]);
    }
}

}
