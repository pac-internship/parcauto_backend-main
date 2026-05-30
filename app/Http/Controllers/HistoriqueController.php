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
use App\Exports\HistoriqueDemandePdf;
use Barryvdh\DomPDF\Facade\Pdf;

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
    public function getHistoriquesChaufeurs(Request $request ){
        try{
            $input = $request->input('body');
            $chauffeur_id = $input['chauffeur_id'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();

            $ligne_notations = DB::select("select `libelle`, round(avg(`valeur`),0) as valeur from `ligne_notations`,
            `critere_notations` where `ligne_notations`.`created_at` >= ? and `ligne_notations`.`created_at` <= ? and `chauffeur_id` = ? and `critere_notations`.`id`=`ligne_notations`.`critere_notation_id`
            group by `libelle`", [$debut, $fin, $chauffeur_id]);

            return response()->json([
                'data' => $ligne_notations,
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
     * Performance Chauffeur Export xls
     */
    public function exportPerformancesChauffeur(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->all();
            $chauffeur_id = $input['chauffeur_id'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            try {
                $file_name = 'PerformancesChauffeur'.$chauffeur_id.'.xls';
                ob_end_clean();
                ob_start();
            return FacadesExcel::download(new ExportPerformancesChauffeur($debut, $fin, $chauffeur_id), $file_name);
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
            ]);
        }
    }

    public function getHistoriquesDemandes(Request $request){
       try{
           // $input = $request->all(); 
            $input = $request->input('body');

            if(!isset($input['date_debut']) || empty($input['date_debut'])){
                $debut = Carbon::now()->subDays(30)->startOfDay();
            } else {
                $debut = Carbon::parse($input['date_debut'])->startOfDay();
            }
            
            if(!isset($input['date_fin']) || empty($input['date_fin'])){
                $fin = Carbon::now()->endOfDay();
            } else {
                $fin = Carbon::parse($input['date_fin'])->endOfDay();
            }

            $query = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($q) {
                 $q->with('entite');
            }, 'chauffeur' => function ($q) {
                $q->with('user');
            }])->whereBetween('created_at', [$debut, $fin]);


            if(!empty($input['vehiculeID'])){
                $query->where('vehicule_id', $input['vehiculeID']);
            }
            
            if(!empty($input['chauffeurID'])){
                $query->where('chauffeur_id', $input['chauffeurID']);
            }
            
            if(!empty($input['point_destination'])){
                $query->where('point_destination', 'like', '%'.$input['point_destination'].'%');
            }
            
            $demandes = $query->get();
            
            $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
            $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
            $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();

            return response()->json([
                'data' => $demandes,
                'demandesNouvelles' => $demandesNouvelles,
                'demandesEncours' => $demandesEncours,
                'demandesTerminees' => $demandesTerminees,
                'message' => $debut,
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
    public function exportHistoriqueDemandesCourses(Request $request){
        if ($request->isMethod('POST')) {
            $input = $request->all();
            $vehicule_id = $input['vehiculeID']?? '';
            $chauffeur_id = $input['chauffeurID']?? '';
            $point_destination = $input['point_destination']?? '';
            $debut = $input['date_debut']?? '';
            $fin = $input['date_fin']?? '';
            $debut = $debut != '' ? Carbon::parse($debut)->startOfDay() : Carbon::now()->startOfWeek()->startOfDay();
            $fin = $fin != '' ? Carbon::parse($fin)->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();
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

    /**
     * Historique Demandes Courses Export PDF
     */
    public function exportHistoriqueDemandesCoursePdf(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->all();
            $vehicule_id = $input['vehiculeID'] ?? '';
            $chauffeur_id = $input['chauffeurID'] ?? '';
            $point_destination = $input['point_destination'] ?? '';
            $debut = $input['date_debut'] ?? '';
            $fin = $input['date_fin'] ?? '';
            $debut = $debut != '' ? Carbon::parse($debut)->startOfDay() : Carbon::now()->startOfWeek()->startOfDay();
            $fin = $fin != '' ? Carbon::parse($fin)->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();
            
            try {
                $exportPdf = new HistoriqueDemandePdf($debut, $fin, $point_destination, $vehicule_id, $chauffeur_id);
                
                $data = [
                    'demandes' => $exportPdf->getFormattedData(),
                    'statistics' => $exportPdf->getStatistics(),
                    'filters' => [
                        'debut' => $debut,
                        'fin' => $fin,
                        'vehicule_id' => $vehicule_id,
                        'chauffeur_id' => $chauffeur_id,
                        'point_destination' => $point_destination,
                    ]
                ];
                
                $file_name = 'Demande_Courses_' . date('d_m_Y') . '.pdf';
                
                $pdf = Pdf::loadView('exports.historique_demandes_pdf', $data)
                    ->setOption('isPhpEnabled', true)
                    ->setOption('isSvgEnabled', true);
                
                return $pdf->download($file_name);
                
            } catch (\Exception $ex) {
                Log::error('PDF Export Error: ' . $ex->getMessage());
                return response()->json([
                    'error' => "error",
                    'message' => "Une erreur interne est survenue lors de la génération du PDF.",
                    'status' => 500
                ]);
            }
        } else {
            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

}
