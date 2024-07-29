<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repair;
use App\Models\VehicleModel;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VehicleModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $vehicle_models = VehicleModel::join('brands','brands.id','=','vehicle_models.brand_id')
                    ->join('users','users.id','=','vehicle_models.created_by')
                    ->join('statuses','statuses.id','=','vehicle_models.status_id')
                    ->select("vehicle_models.*","brands.label as brand_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicle_models.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des modèles.', 
                'vehicle_models' => $vehicle_models,
            ], 
            200
        );
    }

    public function all()
    {
        $vehicle_models = VehicleModel::join('brands','brands.id','=','vehicle_models.brand_id')
                    ->join('users','users.id','=','vehicle_models.created_by')
                    ->join('statuses','statuses.id','=','vehicle_models.status_id')
                    ->select("vehicle_models.*","brands.label as brand_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicle_models.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des modèles.', 
                'vehicle_models' => $vehicle_models
            ], 
            200
        );
    }

    public function createAll(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $table = $request->get('table');

        foreach ($table as $data) {

            $exist = VehicleModel::select("*")
            ->where('label', 'like', $data['label'])
            ->count();  

            if($exist <= 0){
                $vehicle_model = VehicleModel::create(
                    [
                        'label' => $data['label'],
                        'status_id' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }

        }

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Modèle enregistré avec succès.', 
            ], 
            201
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

        $exist = VehicleModel::select("*")
            ->where('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            $vehicle_model = VehicleModel::select("*")
                ->where('label', 'like', $request->label)
                ->first();  

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Ce modèle existe déjà !', 
                    'vehicle_model' => $vehicle_model
                ], 
                201
            );

        } else{
            $this->validate($request, [
                'label' => 'required',
            ]);
    
            $vehicle_model = VehicleModel::create(
                [
                    'label' => $request->label,
                    'brand_id' => $request->brand_id,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
    
            if($vehicle_model){
        
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Modèle enregistré avec succès.', 
                        'vehicle_model' => $vehicle_model
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
        $vehicle_models = VehicleModel::join('brands','brands.id','=','vehicle_models.brand_id')
                    ->join('users','users.id','=','vehicle_models.created_by')
                    ->join('statuses','statuses.id','=','vehicle_models.status_id')
                    ->select("vehicle_models.*","brands.label as brand_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicle_models.created_at")
                    ->where('vehicle_models.id',$id)
                    ->first();           

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des modèles.', 
                'vehicle_model' => $vehicle_model
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

        $exist = VehicleModel::select("*")
            ->where('label', 'like', $request->label)
            ->count();

        $exist_element = VehicleModel::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce modèle existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce modèle existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $vehicle_model = VehicleModel::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'brand_id' => $request->brand_id,
                    'updated_by' => $userId,
                ]
            );

            if($vehicle_model){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du modèle avec succès.', 
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce modèle ne peut être mise à jour en ce moment, veuillez réessayer plus tard !', 
                    ], 
                    400
                );
    
            }
        }

    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $vehicle_model = VehicleModel::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($vehicle_model){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du modèle avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce modèle ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $vehicle_model = VehicleModel::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($vehicle_model){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du modèle avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce modèle ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function search($information)
    {

        $vehicle_models = VehicleModel::join('brands','brands.id','=','vehicle_models.brand_id')
                    ->join('users','users.id','=','vehicle_models.created_by')
                    ->join('statuses','statuses.id','=','vehicle_models.status_id')
                    ->select("vehicle_models.*","brands.label as brand_label","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('vehicle_models.label','like', '%'.$information.'%')
                    ->orderByDesc("vehicle_models.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des modèles.', 
                'vehicle_models' => $vehicle_models,
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