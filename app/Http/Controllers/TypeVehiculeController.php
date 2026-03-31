<?php

namespace App\Http\Controllers;

use App\Models\TypeVehicule;
use Illuminate\Http\Request;

class TypeVehiculeController extends Controller
{
    public function index(){
        try{
            $data = TypeVehicule::all();

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

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

