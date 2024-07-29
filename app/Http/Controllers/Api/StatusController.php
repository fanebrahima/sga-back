<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Status;
use App\Http\Resources\RateResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = Status::select("*")
                    ->orderByDesc("statuses.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des statuts.', 
                'statuses' => $statuses
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

        $exist = Status::select("*")
            ->where([['label', 'like', $request->label],['value','=', $request->value]])
            ->orWhere('label', 'like', $request->label)
            ->orWhere('value','=', $request->value)
            ->count();  

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cet profile existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
                'value' => 'required',
            ]);
    
            $status = Status::create(
                [
                    'label' => $request->label,
                    'value' => $request->value,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );  
    
            if($status){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Statut enregistré avec succès.', 
                        'status' => $status
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce statut ne peut être ajouté en ce moment, veuillez réessayer plus tard !', 
                    ], 
                    201
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
        $status = Status::select('*')
                ->where('id',$id)
                ->find($id);

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail du statut '.$id.'.',
                'status' => $status,
            ], 
            201
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

        $exist = Status::select("*")
            ->where([['label', 'like', $request->label],['value','=', $request->value]])
            ->orWhere('label', 'like', $request->label)
            ->orWhere('value','=', $request->value)
            ->count();

        $exist_element = Status::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce statut existe déjà !', 
                ], 
                201
            );

        } elseif (($exist == 1 && $exist_element->label != $request->label) && ($exist == 1 && $exist_element->value != $request->value)) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce statut existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
                'value' => 'required',
            ]);
            
            $status = Status::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'value' => $request->value,
                    'updated_by' => $userId,
                ]
            );

            if($status){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du statut avec succès.', 
                        'status' => $status
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce statut ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                    ], 
                    201
                );
    
            }
        }

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
