<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repair;
use App\Models\ShockPoint;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ShockPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shock_points = ShockPoint::join('users','users.id','=','shock_points.created_by')
                    ->join('statuses','statuses.id','=','shock_points.status_id')
                    ->select("shock_points.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("shock_points.created_at")
                    ->paginate(10);        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des points de choc.', 
                'shock_points' => $shock_points
            ], 
            200
        );
    }

    public function all()
    {
        $shock_points = ShockPoint::join('users','users.id','=','shock_points.created_by')
                    ->join('statuses','statuses.id','=','shock_points.status_id')
                    ->select("shock_points.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('shock_points.status_id', 1)
                    ->orderByDesc("shock_points.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des points de choc.', 
                'shock_points' => $shock_points
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

            $exist = ShockPoint::select("*")
            ->where('label', 'like', $data['label'])
            ->count();  

            if($exist <= 0){
                $shock_point = ShockPoint::create(
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
                'message' => 'Point de choc enregistré avec succès.', 
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

        $exist = ShockPoint::select("*")
            ->where('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            $shock_point = ShockPoint::select("*")
                ->where('label', 'like', $request->label)
                ->first();  

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Ce point de choc existe déjà !', 
                    'shock_point' => $shock_point
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);

            $label = $request->label;

            if($request->label == 'null'){
                $label = "";
            }
    
            $shock_point = ShockPoint::create(
                [
                    'label' => $label,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
    
            if($shock_point){
        
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Point de choc enregistré avec succès.', 
                        'shock_point' => $shock_point
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
        $shock_point = ShockPoint::join('users','users.id','=','shock_points.created_by')
                    ->join('statuses','statuses.id','=','shock_points.status_id')
                    ->select("shock_points.*","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("shock_points.created_at")
                    ->where('shock_points.id',$id)
                    ->first();           

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des points de choc.', 
                'shock_point' => $shock_point
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

        $exist = ShockPoint::select("*")
            ->where('label', 'like', $request->label)
            ->count();

        $exist_element = ShockPoint::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce point de choc existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce point de choc existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $shock_point = ShockPoint::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'updated_by' => $userId,
                ]
            );

            if($shock_point){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du point de choc avec succès.', 
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce point de choc ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
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

        $shock_point = ShockPoint::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($shock_point){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du point de choc avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce point de choc ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $shock_point = ShockPoint::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($shock_point){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour du point de choc avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce point de choc ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function search($information)
    {
        $shock_points = ShockPoint::join('users','users.id','=','shock_points.created_by')
                    ->join('statuses','statuses.id','=','shock_points.status_id')
                    ->select("shock_points.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('shock_points.label','like', '%'.$information.'%')
                    ->orderByDesc("shock_points.created_at")
                    ->paginate(10);        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des points de choc.', 
                'shock_points' => $shock_points
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
