<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status"=> "error",  'message'=> "Paramètres incorrects" ], 401);  //
            }

            $loginData = $request->only('email', 'password');
            
            if (!auth()->attempt($loginData)) {
                return response([ "status"=> "erreur", 'message' => 'Paramètres invalides']);
            }
            // si le user est actif

            if(auth()->user() != null ) {
                $user = auth()->user(); $user->load('role');
                $accessToken = auth()->user()->createToken('authToken')->accessToken;
                return response(['user' => $user, 'access_token' => $accessToken, "status"=> "success",]);
            } 
        }catch(Exception $ex){

            Log::error($ex->getMessage());

            return response()->json([
                'error'=>"error",
                'message'=>"Une erreur interne est survenue.",
                'statut'=>500
            ],500);
        }
    }

    
}