<?php

namespace App\Http\Controllers;
use App\Models\Direction;
use App\Models\DemandeVehicule;
use App\Models\CritereNotation;
use App\Models\AffectationDemande;
use App\Models\LigneNotation;
use Exception;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

use Carbon\Carbon;

use App\Exports\HistoriqueDemandes;
use App\Exports\ExportPerformancesChauffeur;

use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public $chauffeur_id;
    public $vehicule_id;
    public $export_demandes;
    //Liste des Directions
    public function getDirections(){
        try{
            $direction = Direction::get();

            return response()->json([
                'data' => $direction,
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

    //Historique des Performances Chauffeurs
   // ✅ getHistoriquesChaufeurs : les données arrivent en JSON body direct
public function getHistoriquesChaufeurs(Request $request)
{
    try {
        // ✅ FIX: $request->all() au lieu de $request->input('body')
        $input = $request->all();
        
        $chauffeur_id = $input['chauffeur_id'] ?? null;
        $debut = $input['date_debut'] ?? now()->startOfDay();
        $fin   = $input['date_fin']   ?? now()->endOfDay();

        $debut = Carbon::parse($debut)->startOfDay();
        $fin   = Carbon::parse($fin)->endOfDay();

        // ✅ Si chauffeur_id est vide, retourner toutes les moyennes (tous chauffeurs)
        if ($chauffeur_id) {
            $ligne_notations = DB::select("
                SELECT cn.libelle,
                       ROUND(AVG(ln.valeur), 0) AS valeur
                FROM ligne_notations ln
                JOIN critere_notations cn ON cn.id = ln.critere_notation_id
                WHERE ln.created_at >= ?
                  AND ln.created_at <= ?
                  AND ln.chauffeur_id = ?
                GROUP BY cn.libelle
                ORDER BY cn.libelle
            ", [$debut, $fin, $chauffeur_id]);
        } else {
            // Tous chauffeurs confondus
            $ligne_notations = DB::select("
                SELECT cn.libelle,
                       ROUND(AVG(ln.valeur), 0) AS valeur
                FROM ligne_notations ln
                JOIN critere_notations cn ON cn.id = ln.critere_notation_id
                WHERE ln.created_at >= ?
                  AND ln.created_at <= ?
                GROUP BY cn.libelle
                ORDER BY cn.libelle
            ", [$debut, $fin]);
        }

        return response()->json([
            'data'    => $ligne_notations,
            'message' => '',
            'status'  => 200,
        ], 200);

    } catch (Exception $ex) {
        Log::error($ex->getMessage());
        return response()->json([
            'error'   => 'error',
            'message' => 'Une erreur interne est survenue.',
            'status'  => 500,
        ]);
    }
}

// ✅ exportPerformancesChauffeur : adapter si le frontend envoie body directement
public function exportPerformancesChauffeur(Request $request)
{
    if ($request->isMethod('POST')) {
        $input = $request->all(); // ✅ FIX: plus de wrapper 'body'

        $chauffeur_id = $input['chauffeur_id'] ?? '';
        $debut = Carbon::parse($input['date_debut'] ?? now())->startOfDay();
        $fin   = Carbon::parse($input['date_fin']   ?? now())->endOfDay();

        try {
            $file_name = 'PerformancesChauffeur_' . $chauffeur_id . '.xls';
            ob_end_clean();
            ob_start();
            return FacadesExcel::download(
                new ExportPerformancesChauffeur($debut, $fin, $chauffeur_id),
                $file_name
            );
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error'   => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status'  => 500,
            ]);
        }
    }

    return response()->json(['error' => 'error', 'status' => 500]);
}
    //Historique Demande
    public function getHistoriquesDemandes(Request $request){
        try{

            $input = $request->input('body');
            $demandes = [];
            $demandesNouvelles = 0;
            $demandesEncours = 0;
            $demandesTerminees = 0;

            //Parmas date_debut and date_fin
            if(!$input['vehiculeID'] && !$input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and véhicule
            if($input['vehiculeID'] && !$input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('vehicule_id', $input['vehiculeID'])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and chauffeur
            if(!$input['vehiculeID'] && $input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('chauffeur_id', $input['chauffeurID'])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and destination
            if(!$input['vehiculeID'] && !$input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //All Parmas is set
            if($input['vehiculeID'] && $input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('chauffeur_id', $input['chauffeurID'])
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas Chauffeur + Vehicule
            if($input['vehiculeID'] && $input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('chauffeur_id', $input['chauffeurID'])
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas is set Chauffeur + Destination
            if(!$input['vehiculeID'] && $input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('chauffeur_id', $input['chauffeurID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas Vehicule + Destination
            if($input['vehiculeID'] && !$input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            $this->export_demandes = $demandes;

            return response()->json([
                'data' => $demandes,
                'demandesNouvelles' => $demandesNouvelles,
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

    /**
     * Journal Caisse Export xls
     */
    public function exportHistoriqueDemandesCourses(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->input();
            $vehicule_id = $input['vehiculeID'];
            $chauffeur_id = $input['chauffeurID'];
            $point_destination = $input['point_destination'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            try {
                $file_name = 'Demande_Courses'.$fin.'.xls';
                ob_end_clean();
                ob_start();
            return FacadesExcel::download(new HistoriqueDemandes($debut, $fin, $point_destination, $vehicule_id, $chauffeur_id), $file_name);
            } catch (\Exception $ex) {
                return response()->json([
                    'error' => "error",
                    'message' => "Une erreur interne est survenue.",
                    'status' => 500
                ]);
            }
        } else {
            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);        }
    }


    public function getHistoriquePerformancesChauffeur(Request $request)
{
    try {
        // Exemple simple
        $data = []; // à adapter selon ta logique

        return response()->json([
            'success' => 'success',
            'data' => $data,
            'status' => 200
        ]);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());

        return response()->json([
            'error' => 'error',
            'message' => 'Erreur interne',
            'status' => 500
        ]);
    }
}

}
