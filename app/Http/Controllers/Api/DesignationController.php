<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repair;
use App\Models\Designation;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $designations = Designation::join('users','users.id','=','designations.created_by')
                    ->join('statuses','statuses.id','=','designations.status_id')
                    ->select("designations.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("designations.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des désignations.', 
                'designations' => $designations,
            ], 
            200
        );
    }

    public function all()
    {
        $designations = Designation::join('users','users.id','=','designations.created_by')
                    ->join('statuses','statuses.id','=','designations.status_id')
                    ->select("designations.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('designations.status_id', 1)
                    ->orderByDesc("designations.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des désignations.', 
                'designations' => $designations
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

            $exist = Designation::select("*")
            ->where('label', 'like', $data['label'])
            ->count();  

            if($exist <= 0){
                $designation = Designation::create(
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
                'message' => 'Désignation enregistrée avec succès.', 
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

        $exist = Designation::select("*")
            ->where('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            $designation = Designation::select("*")
                ->where('label', 'like', $request->label)
                ->first();  

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Cette désignation existe déjà !', 
                    'designation' => $designation
                ], 
                201
            );

        } else{
            $this->validate($request, [
                'label' => 'required',
            ]);
    
            $designation = Designation::create(
                [
                    'label' => $request->label,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
    
            if($designation){
        
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Désignation enregistrée avec succès.', 
                        'designation' => $designation
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
        $designation = Designation::join('users','users.id','=','designations.created_by')
                    ->join('statuses','statuses.id','=','designations.status_id')
                    ->select("designations.*","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("designations.created_at")
                    ->where('designations.id',$id)
                    ->first();           

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des désignations.', 
                'designation' => $designation
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

        $exist = Designation::select("*")
            ->where('label', 'like', $request->label)
            ->count();

        $exist_element = Designation::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette désignation existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette désignation existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $designation = Designation::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'updated_by' => $userId,
                ]
            );

            if($designation){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour de la désignation avec succès.', 
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Cette désignation ne peut être mise à jour en ce moment, veuillez réessayer plus tard !', 
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

        $designation = Designation::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($designation){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la désignation avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette désignation ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $designation = Designation::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($designation){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la désignation avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette désignation ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function search($information)
    {

        $designations = Designation::join('users','users.id','=','designations.created_by')
                    ->join('statuses','statuses.id','=','designations.status_id')
                    ->select("designations.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('designations.label','like', '%'.$information.'%')
                    ->orderByDesc("designations.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des désignations.', 
                'designations' => $designations,
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
