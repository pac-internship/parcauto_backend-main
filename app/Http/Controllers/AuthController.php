<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status"=> "error",
                    "message"=> "Paramètres incorrects"
                ],401);
            }

            $loginData = $request->only('email', 'password');

            if (!auth()->attempt($loginData)) {
                return response()->json([
                    "status"=> "error",
                    "message" => "Paramètres invalides"
                ],401);
            }

            // utilisateur connecté
            $user = auth()->user()->load('role'); // 🔥 récupération du rôle

            $accessToken = $user->createToken('authToken')->accessToken;

            return response()->json([
                "status" => "success",
                "user" => $user,
                "role" => $user->role, // 🔥 rôle renvoyé
                "access_token" => $accessToken
            ]);

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