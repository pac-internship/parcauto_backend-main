<?php

use App\Models\TypeVehicule;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\DemandeCourseController;
use App\Http\Controllers\PlanningGardeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CritereDeNotationController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\JournalSmsController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::group(['prefix' => 'user'], function () {
    // load-by-email
    Route::post('load-by-email', [App\Http\Controllers\UserController::class, 'loadUserByEmail'])->name('user.load-user-by-email');
});

Route::group(['middleware' => 'auth:api'], function () {  //Cela garantit que toutes tes routes protégées utilisent le système Passport.



    Route::group(['prefix' => 'parc'], function (){
        Route::get('type-vehicule', [VehiculeController::class, 'getTypesVehicules'])->name('type-vehicule');
        Route::get('getAllUser', [UserController::class, 'getAllUser'])->name('get-user');
        Route::get('getstatistiquebyrole/{user_id}/{role}', [StatistiqueController::class, 'getDemandesStatistiqueByRole'])->name('get-statistique');

        Route::get('get-user-by-id/{userId}', [UserController::class, 'getUserById'])->name('get-user-by-id');

        Route::post('user/update', [UserController::class, 'updateUser'])->name('user-update');
        Route::post('user/delete', [UserController::class, 'deleteUser'])->name('user-delete');

        Route::get('motif', [MotifController::class, 'getMotif'])->name('motif');
        Route::post('save/demande-courses', [DemandeCourseController::class, 'saveDemande'])->name('demande-courses-save');
        Route::get('list/demande-courses/{user_id}/{role}', [DemandeCourseController::class, 'listDemandeVehicule'])->name('demande-courses-list');
        Route::get('affecter/demande-courses/{typeVehiculeId}/{demande_id}', [DemandeCourseController::class, 'getAttributaffecterDemande'])->name('demande-courses-affectation');
        Route::get('affecter/demande-courses/historique', [DemandeCourseController::class, 'getDemandeAffecte'])->name('demande-courses-affectation-historique');
        Route::post('affecter/demande-courses', [DemandeCourseController::class, 'affecterDemande'])->name('demande-courses-affectation');
        Route::get('criterDeNotation/getCritereNotation',[CritereDeNotationController::class,'getCritereNotation'])->name('get-critere-notation');
        Route::post('note/newNote',[NoteController::class,'saveNewNotes'])->name('post-new-notes');
        Route::get('note/getNote/{demande_id}',[NoteController::class,'getNoteByIdDemande'])->name('get-note-by-demande');
        Route::post('filter',[DemandeCourseController::class,'filterDemandeCourse'])->name('filter-demande-course');
        Route::get('list/demande-courses-en-cour/{user_id}/{role}',[DemandeCourseController::class,'getDemandeCourseEnCour'])->name('demande-course-en-cour');
        Route::post('list/demande-courses-en-cour-filtrer',[DemandeCourseController::class,'getDemandeCourseEnCourFiltrer'])->name('demande-course-en-cour-filtrer');
        Route::post('affecter/update',[DemandeCourseController::class,'updateAffectation'])->name('affectation-update');
        Route::get('verifierNotation/{user_id}',[DemandeCourseController::class,'verifierNotation'])->name('get-note-by-demande-id');
        Route::get('verify-seats/{demande_id}',[DemandeCourseController::class,'countPlace'])->name('count-by-demande-id');


    });


    // Vehicule routes
    Route::prefix('vehicule')->group(function () {
        Route::get('type', [VehiculeController::class, 'getTypesVehicules']);
        Route::post('save-type', [VehiculeController::class, 'saveTypeVehicule']);

        Route::get('categorie-permis', [VehiculeController::class, 'getCategoriePermis']);
        Route::post('save-categorie-permis', [VehiculeController::class, 'saveCategoriePermis']);

        Route::post('save-conduire', [VehiculeController::class, 'saveConduire']);
        Route::post('delete-conduire', [VehiculeController::class, 'deleteConduire']);

        Route::get('get-vehicule-by-id/{vehiculeId}', [VehiculeController::class, 'getVehiculeById'])->name('get-vehicule-by-id');

        Route::get('list', [VehiculeController::class, 'getVehicules']);
        Route::post('save', [VehiculeController::class, 'saveVehicule']);
    });

    // Chauffeur routes
    Route::prefix('chauffeur')->group(function () {
        Route::get('list', [ChauffeurController::class, 'getChauffeurs']);
        Route::get('list-agents', [ChauffeurController::class, 'getAgents']);
        Route::post('save', [ChauffeurController::class, 'saveChauffeur']);
        Route::get('get-chauffeur-by-id/{chauffeurId}', [ChauffeurController::class, 'getChauffeurById'])->name('get-vehicule-by-id');

        Route::get('delete/planning-garde/{planningId}', [PlanningGardeController::class, 'deletePlanningGardes'])->name('delete-planning-garde');
        Route::get('get-planning-by-id/{planningId}', [PlanningGardeController::class, 'getPlanningGardesById'])->name('delete-planning-garde');
        Route::get('list/planning-gardes', [PlanningGardeController::class, 'getPlanningGardes']);
        Route::post('save/planning-garde', [PlanningGardeController::class, 'savePlanningGardes']);

        Route::post('download-planning', [PlanningGardeController::class,'downloadPlanning']);
        Route::get('get-planning-path/{planning_garde_id}', function ($planning_garde_id)
        {
            $file_path = 'app/public/plannings/Planning_Garde'.$planning_garde_id.'.pdf';
            return response()->file(storage_path($file_path));
        });

        Route::get('motif', [MotifController::class, 'getMotif'])->name('motif');
        Route::post('save/demande-courses', [DemandeCourseController::class, 'saveDemande'])->name('demande-courses-save');
        Route::get('list/demande-courses', [DemandeCourseController::class, 'listDemandeVehicule'])->name('demande-courses-list');
        Route::get('get-demande-by-id/{demandeId}', [DemandeCourseController::class, 'getDemandeCourseById'])->name('demande-course-by-id');
        Route::post('delete/demande-course', [DemandeCourseController::class, 'deleteDemandeCourse'])->name('demande-course-delete');
        Route::post('edit/demande-course', [DemandeCourseController::class, 'editDemande'])->name('demande-courses-edit');
        Route::get('demande-courses/demarer/{demandeId}',[DemandeCourseController::class,'demmarerCourse'])->name('demmarer_course');
        Route::get('demande-courses/arreter/{demandeId}',[DemandeCourseController::class,'arreterCourse'])->name('arreter_course');
        Route::get('verify-chauffeur/{chauffeur}/{demandeCourseId}',[DemandeCourseController::class,'verifiedChauffeurAffectation'])->name('affectation-chauffeur');


    });

    // Historiques routes
    Route::prefix('historiques')->group(function () {
        Route::get('list-directions', [HistoriqueController::class, 'getDirections']);
        Route::post('get-historique-demandes', [HistoriqueController::class, 'getHistoriquesDemandes']);
        Route::post('get-historique-performaces-chauffeur', [HistoriqueController::class, 'getHistoriquesChaufeurs']);

        Route::post('export-performances-chauffeur', [HistoriqueController::class, 'exportPerformancesChauffeur']);
        Route::post('export-historique-demandes', [HistoriqueController::class, 'exportHistoriqueDemandesCourses']);
    });

    // Journal des SMS routes
    Route::group(['prefix' => 'journal-sms'], function () {

        Route::get('list', [JournalSmsController::class, 'getAllSMS'])->name('sms-list');
        Route::post('search',[JournalSmsController::class,'searchSMS'])->name('search-sms');

    });

});
