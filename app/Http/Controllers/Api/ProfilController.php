<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Profil;
use App\Http\Resources\RateResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profils = Profil::join('statuses', 'statuses.id', '=', 'profils.status_id')
                ->select("profils.*")
                ->where("profils.status_id",1)
                ->orderByDesc("profils.created_at")
                ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des profiles.', 
                'profils' => $profils
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

        $exist = Profil::select("*")
            ->where('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce profile existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
    
            $profil = Profil::create(
                [
                    'label' => $request->label,
                    'description' => $request->description,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );  
    
            if($profil){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Profile enregistré avec succès.', 
                        'profil' => $profil
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce profile ne peut être ajouté en ce moment, veuillez réessayer plus tard !', 
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
        $profil = Profil::select('*')
                ->where('id',$id)
                ->find($id);

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail de la profile '.$id.'.',
                'profil' => $profil,
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

        $exist = Profil::select("*")
            ->where('label', 'like', $request->label)
            ->count();

        $exist_element = Profil::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce profile existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce profile existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $profil = Profil::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'description' => $request->description,
                    'updated_by' => $userId,
                ]
            );

            if($profil){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour de la profile avec succès.', 
                        'profil' => $profil
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce profile ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
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
