<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repair;
use App\Models\Color;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $colors = Color::join('users','users.id','=','colors.created_by')
                    ->join('statuses','statuses.id','=','colors.status_id')
                    ->select("colors.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("colors.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des couleurs.', 
                'colors' => $colors,
            ], 
            200
        );
    }

    public function all()
    {
        $colors = Color::join('users','users.id','=','colors.created_by')
                    ->join('statuses','statuses.id','=','colors.status_id')
                    ->select("colors.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('colors.status_id', 1)
                    ->orderByDesc("colors.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des couleurs.', 
                'colors' => $colors
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

            $exist = Color::select("*")
            ->where('label', 'like', $data['label'])
            ->count();  

            if($exist <= 0){
                $color = Color::create(
                    [
                        'label' => strtoupper($data['label']),
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
                'message' => 'Couleur enregistrée avec succès.', 
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

        $exist = Color::select("*")
            ->where('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            $color = Color::select("*")
                ->where('label', 'like', $request->label)
                ->first();  

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Cette couleur existe déjà !', 
                    'color' => $color
                ], 
                201
            );

        } else{
            $this->validate($request, [
                'label' => 'required',
            ]);
    
            $color = Color::create(
                [
                    'label' => strtoupper($request->label),
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
    
            if($color){
        
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Couleur enregistrée avec succès.', 
                        'color' => $color
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
        $color = Color::join('users','users.id','=','colors.created_by')
                    ->join('statuses','statuses.id','=','colors.status_id')
                    ->select("colors.*","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("colors.created_at")
                    ->where('colors.id',$id)
                    ->first();           

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des couleurs.', 
                'color' => $color
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

        $exist = Color::select("*")
            ->where('label', 'like', $request->label)
            ->count();

        $exist_element = Color::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette couleur existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette couleur existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $color = Color::where('id', $request->id)->update(
                [
                    'label' => strtoupper($request->label),
                    'updated_by' => $userId,
                ]
            );

            if($color){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour de la couleur avec succès.', 
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Cette couleur ne peut être mise à jour en ce moment, veuillez réessayer plus tard !', 
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

        $color = Color::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($color){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la couleur avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette couleur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $color = Color::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($color){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la couleur avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette couleur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function search($information)
    {

        $colors = Color::join('users','users.id','=','colors.created_by')
                    ->join('statuses','statuses.id','=','colors.status_id')
                    ->select("colors.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('colors.label','like', '%'.$information.'%')
                    ->orderByDesc("colors.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des couleurs.', 
                'colors' => $colors,
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
