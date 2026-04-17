<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\DemandeCourseController;
use App\Http\Controllers\PlanningGardeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CritereDeNotationController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\JournalSmsController;
use App\Http\Controllers\API\StatistiqueController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\EntiteController;
use App\Http\Controllers\TypeVehiculeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/
//Route::get('/parc/typevehicule', [TypeVehiculeController::class, 'index']);
/**
 * Authentification
 */
Route::post('/login', [AuthController::class, 'login']);

/**
 * Réinitialisation mot de passe
 */
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

/**
 * Public routes
 */
Route::prefix('user')->group(function () {
    Route::post('load-by-email', [UserController::class, 'loadUserByEmail']);
});

/**
 * Routes protégées par auth:api
 */
Route::middleware('auth:api')->group(function () {

    /**
     * Dashboard
     */
    Route::get('dashboard/stats', [DashboardController::class, 'getDashboardStats']);

    /**
     * Utilisateurs
     */
    Route::prefix('user')->group(function () {
        Route::get('all', [UserController::class, 'getAllUser']);
        Route::get('get/{userId}', [UserController::class, 'getUserById']);
        Route::post('update', [UserController::class, 'updateUser']);
        Route::post('delete', [UserController::class, 'deleteUser']);
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::post('/save', [UserController::class, 'saveUser']);
    });

    /**
     * Véhicules
     */
    Route::prefix('vehicule')->group(function () {
        Route::get('type', [VehiculeController::class, 'getTypesVehicules']);
        Route::post('save-type', [VehiculeController::class, 'saveTypeVehicule']);
        Route::get('categorie-permis', [VehiculeController::class, 'getCategoriePermis']);
        Route::post('save-categorie-permis', [VehiculeController::class, 'saveCategoriePermis']);
        Route::post('save-conduire', [VehiculeController::class, 'saveConduire']);
        Route::post('delete-conduire', [VehiculeController::class, 'deleteConduire']);
        Route::get('get/{vehiculeId}', [VehiculeController::class, 'getVehiculeById']);
        Route::get('list', [VehiculeController::class, 'getVehicules']);
        Route::post('save', [VehiculeController::class, 'saveVehicule']);
        Route::delete('/vehicules/{id}', [VehiculeController::class, 'deleteVehicule']);
    });

    /**
     * Chauffeurs
     */
    Route::prefix('chauffeur')->group(function () {
        Route::get('list', [ChauffeurController::class, 'getChauffeurs']);
        Route::post('save', [ChauffeurController::class, 'saveChauffeur']);
        Route::get('get/{chauffeurId}', [ChauffeurController::class, 'getChauffeurById']);
        Route::post('update-disponibilite/{chauffeurId}', [ChauffeurController::class, 'updateDisponibilite']);
    });

    /**
     * Demandes de courses
     */
    Route::prefix('demande')->group(function () {
        Route::post('save', [DemandeCourseController::class, 'saveDemande']);
        Route::get('list/{user_id}/{role}', [DemandeCourseController::class, 'listDemandeVehicule']);
        Route::get('get/{demandeId}', [DemandeCourseController::class, 'getDemandeCourseById']);
        Route::post('edit', [DemandeCourseController::class, 'editDemande']);
        Route::post('delete', [DemandeCourseController::class, 'deleteDemandeCourse']);
        Route::get('en-cours/{user_id}/{role}', [DemandeCourseController::class, 'getDemandeCourseEnCour']);
        Route::post('filtrer-en-cours', [DemandeCourseController::class, 'getDemandeCourseEnCourFiltrer']);
    });

  /**
     * Affectations
     */
    Route::prefix('affectation')->group(function () {
        Route::get('list', [DemandeCourseController::class, 'getDemandeAffecte']);
        Route::get('attributs/{typeVehiculeId}/{demande_id}', [DemandeCourseController::class, 'getAttributaffecterDemande']);
        Route::post('save', [DemandeCourseController::class, 'affecterDemande']);
        Route::post('update', [DemandeCourseController::class, 'updateAffectation']);
    });
    
    /**
     * Notations
     */
    Route::prefix('notation')->group(function () {
        Route::get('criteres', [CritereDeNotationController::class, 'getCritereNotation']);
        Route::post('save', [NoteController::class, 'saveNewNotes']);
        Route::get('get/{demande_id}', [NoteController::class, 'getNoteByIdDemande']);
    });

    /**
     * Planning de garde
     */
    Route::prefix('planning')->group(function () {
        Route::get('list', [PlanningGardeController::class, 'getPlanningGardes']);
        Route::get('get/{planningId}', [PlanningGardeController::class, 'getPlanningGardesById']);
        Route::get('delete/{planningId}', [PlanningGardeController::class, 'deletePlanningGardes']);
        Route::post('save', [PlanningGardeController::class, 'savePlanningGardes']);
        Route::post('download', [PlanningGardeController::class, 'downloadPlanning']);
    });

    /**
     * Historiques
     */
    Route::prefix('historiques')->group(function () {
        Route::get('directions', [HistoriqueController::class, 'getDirections']);
        Route::post('demandes', [HistoriqueController::class, 'getHistoriquesDemandes']);
        Route::post('chauffeurs', [HistoriqueController::class, 'getHistoriquesChaufeurs']);
        Route::post('export-chauffeurs', [HistoriqueController::class, 'exportPerformancesChauffeur']);
        Route::post('export-demandes', [HistoriqueController::class, 'exportHistoriqueDemandesCourses']);
        Route::post('get-historique-performances-chauffeur', [HistoriqueController::class, 'getHistoriquePerformancesChauffeur']);
    });

    /**
     * Journal SMS
     */
    Route::prefix('journal-sms')->group(function () {
        Route::get('list', [JournalSmsController::class, 'getAllSMS']);
        Route::post('search', [JournalSmsController::class, 'searchSMS']);
    });

   Route::prefix('parc')->group(function () {

    // utilisateurs
    Route::get('getAllUser', [UserController::class, 'getAllUser']);

    // motifs
    Route::get('motif', [MotifController::class, 'index']);

    // typevehicule
    Route::get('typevehicule', [TypeVehiculeController::class, 'index']);

    // demandes de courses
    Route::get('list/demande-courses/{user_id}/{role}', [DemandeCourseController::class, 'listDemandeVehicule']);
    Route::get('list/demande-courses-en-cour/{user_id}/{role}', [DemandeCourseController::class, 'getDemandeCourseEnCour']);

    // vérification notation
    Route::get('verifierNotation/{demande_id}', [NoteController::class, 'verifierNotation']);

});

    /**
     * Statistiques
     *
     * Recapitulatif : les noms de méthodes pointés ici correspondent aux méthodes
     * présentes dans le  StatistiqueController fourni (statistiquesMensuelles,
     * statistiquesTrimestrielles, etc.).
     * nom (getAffectationsParMois, topVehiculesUtilises, exportStatistiquesExcel,)
     */
    /**
 * Statistiques
 */
    Route::prefix('statistique')->group(function () {
        Route::get('repartition-chauffeurs', [StatistiqueController::class, 'repartitionChauffeursDisponibilite']);

    // Statistiques mensuelles / trimestrielles
        Route::get('demandes-mensuelles', [StatistiqueController::class, 'statistiquesMensuelles']);
        Route::get('demandes-trimestrielles', [StatistiqueController::class, 'statistiquesTrimestrielles']);

    // Export demandes / exports Excel
        Route::get('export-demandes-vehicules', [StatistiqueController::class, 'exportDemandesVehicules']);
        Route::get('export-demandes-statistiques', [StatistiqueController::class, 'exportDemandesStatistiques']);

    // Liste des demandeurs (JSON) et export Excel
        Route::get('demandeurs', [StatistiqueController::class, 'listeDemandeurs']);
        Route::post('export-liste-demandeurs', [StatistiqueController::class, 'exportListeDemandeurs']);

    // 📌 Nouvelle route export liste demandeurs (Excel)
        Route::get('liste-demandeurs-export', [StatistiqueController::class, 'ListeDemandeursExport']);
});


    /**
     * Entités
     */
    Route::apiResource('entites', EntiteController::class);

});
