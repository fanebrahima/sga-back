<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('statuses','statuses.id','=','vehicles.status_id')
                    ->join('users','users.id','=','vehicles.created_by')
                    ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicles.created_at")
                    ->paginate(10);        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des véhicules.', 
                'vehicles' => $vehicles
            ], 
            200
        );
    }

    public function all()
    {
        $vehicles = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('statuses','statuses.id','=','vehicles.status_id')
                    ->join('users','users.id','=','vehicles.created_by')
                    ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('vehicles.status_id', 1)
                    ->orderByDesc("vehicles.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des véhicules.', 
                'vehicles' => $vehicles
            ], 
            200
        );
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {    
        $user = Auth::user();
        $userId = $user->id;

        $license_plate = str_replace(' ', '', $request->license_plate);

        $exist = Vehicle::select("*")
            ->where('license_plate', 'like', $license_plate)
            ->count();  

        if($exist > 0){

            $vehicle = Vehicle::select("*")
                ->where('license_plate', 'like', $license_plate)
                ->first();  

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Ce véhicule existe déjà !', 
                    'vehicle' => $vehicle
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'license_plate' => 'required',
                'brand_id' => 'required',
                'model' => 'required',
            ]);

            // $license_plate = $request->license_plate;
            $brand_id = $request->brand_id;
            $model = $request->model;
            $type = $request->type;
            $option = $request->option;
            $color_id = $request->color_id;
            $mileage = $request->mileage;

            // if($request->license_plate == 'null'){
            //     $license_plate = "";
            // }
            if($request->brand_id == 'null'){
                $brand_id = "";
            }
            if($request->model == 'null'){
                $model = "";
            }
            if($request->type == 'null'){
                $type = "";
            }
            if($request->option == 'null'){
                $option = "";
            }
            if($request->color_id == 'null'){
                $color_id = "";
            }
            if($request->mileage == 'null'){
                $mileage = "";
            }
    
            $vehicle = Vehicle::create(
                [
                    'license_plate' => strtoupper($license_plate),
                    'brand_id' => $brand_id,
                    'model' => $model,
                    'type' => $type,
                    'option' => $option,
                    'color_id' => $color_id,
                    'mileage' => $mileage,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
    
            if($vehicle){
        
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Véhicule enregistré avec succès.', 
                        'vehicle' => $vehicle
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Opération impossible en ce moment, veuillez réessayer plus tard !', 
                    ], 
                    400
                );
    
            }

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('statuses','statuses.id','=','vehicles.status_id')
                    ->join('users','users.id','=','vehicles.created_by')
                    ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicles.created_at")
                    ->where('vehicles.id',$id)
                    ->first();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des véhicules.', 
                'vehicle' => $vehicle
            ], 
            200
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $license_plate = str_replace(' ', '', $request->license_plate);

        $exist = Vehicle::select("*")
            ->where('license_plate', 'like', $license_plate)
            ->count();

        $exist_element = Vehicle::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce véhicule existe déjà 1 !', 
                    "exist" => $exist
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->license_plate != $request->license_plate) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce véhicule existe déjà 2 !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'license_plate' => 'required',
                'brand_id' => 'required',
                'model' => 'required',
            ]);
            
            $vehicle = Vehicle::where('id', $request->id)->update(
                [
                    'license_plate' => strtoupper($exist_element->license_plate),
                    'brand_id' => $request->brand_id,
                    'model' => $request->model,
                    'type' => $request->type,
                    'option' => $request->option,
                    'color_id' => $request->color_id,
                    'mileage' => $request->mileage,
                    'updated_by' => $userId,
                ]
            );

            if($vehicle){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du véhicule avec succès.', 
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce véhicule ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                    ], 
                    201
                );
    
            }
        }

    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $vehicle = Vehicle::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($vehicle){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du véhicule avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce véhicule ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                201
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $vehicle = Vehicle::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($vehicle){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du véhicule avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce véhicule ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                201
            );

        }

    }

    public function search($information)
    {
        $vehicles = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('statuses','statuses.id','=','vehicles.status_id')
                    ->join('users','users.id','=','vehicles.created_by')
                    ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('vehicles.license_plate','like', '%'.$information.'%')
                    ->orderByDesc("vehicles.created_at")
                    ->paginate(10);        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des véhicules.', 
                'vehicles' => $vehicles
            ], 
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}