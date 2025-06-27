<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getAllUser(){
        try{
            $user = User::with('categorieUser')
            ->with('direction')
            ->with('role')
            ->whereIn('statut',[true])
            ->orderBy('created_at')
            ->get();

            return response()->json([
                'data' => $user,
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

    public function getUserById($userId){
        try{
            $data =  User::where('id', $userId)->first();

            return response()->json([
                'data' => $data,
                'success' => "success",
                'status' => 200
            ], 200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status'  => 500
            ], 500);
        }
    }

    public function updateUser(Request $request){
        try{

            $nom = $request->input('nom');
            $prenom = $request->input('prenom');
            $email = $request->input('email');
            $user_id = $request->input('user_id');

            $user = User::where('id',$user_id)->first();
            $user->nom = $nom;
            $user->prenom = $prenom;
            $user->email = $email;
            $user->save();
            DB::commit();

            return response()->json([
                'data' => '',
                'message' => 'Utilisateur modifié avec succès',
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

    public function deleteUser(Request $request){
        try{
            $user_id = $request[0];

            $user = User::where('id',$user_id)->first();

            $user->statut = false;
            $user->save();
            DB::commit();

            return response()->json([
                'data' => '',
                'message' => 'Utilisateur désactivé avec succès',
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

    public function loadUserByEmail(Request $request){
        try {
            $email = $request->input('email');
            $user = User::query()
                ->with('role','demandeVehicule','direction','categorieUser')
                ->where('email', '=', $email)->first();
            if($user == null) {
                return response()->json([
                    'error' => "error",
                    'message' => "Utilisateur introuvable.",
                    'status' => 404
                ], 404);
            }
            return response()->json([
                'data' => $user,
                'success' => "success",
                'message' => "",
                'status' => 200
            ], 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ], 500);
        }
    }
}
