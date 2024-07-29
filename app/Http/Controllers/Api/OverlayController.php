<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Partner;
use App\Models\Overlay;
use App\Models\OverlayStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OverlayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overlays = Overlay::join('statuses','statuses.id','=','overlays.status_id')
                    ->select("overlays.*","statuses.value as status_value","statuses.label as status_label")
                    ->where('overlays.etat',1)
                    ->orderByDesc("overlays.updated_at")
                    ->get();

        //$user = User::find($userId);
        //$token = $user->createToken('myapptoken')->plainTextToken;
        //$a = $user->name;
        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des dossiers.', 
                'overlays' => $overlays
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
        $userPartnerId = $user->partner_id;

        $annee = date("Y");
        $mois_jour_heure = date("mdh");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $overlay_number = 'MIS'.$today;

        $this->validate($request, [
            'number' => 'required',
            'label' => 'required',
            'status_id' => 'required',
        ]);

        $files = [];
        if($request->hasfile('pictures'))
        {
            $count = 0;
            foreach($request->file('pictures') as $file)
            {
                $count = $count + 1;
                $name = $today.$count.'.'.$file->extension();
                $file->move(public_path('uploads/article'), $name);  
                $files[] = $name;  
            }
        }

        $overlay = Overlay::create(
            [
                'number' => $overlay_number,
                'label' => $request->label,
                'status_id' => $request->status_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $overlay_status = OverlayStatus::create(
            [
                'overlay_id' => $overlay->id,
                'status_id' => $request->status_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );


        if($overlay && $overlay_status){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Dossier enregistré avec succès.', 
                    'overlay' => $overlay
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce dossier ne peut être enregistré en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

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
    public function show($uuid)
    {
        $overlay = Overlay::join('statuses','statuses.id','=','overlays.status_id')
            ->select("overlays.*","statuses.value as status_value","statuses.label as status_label")
            ->where('overlays.uuid',$uuid)
            ->first();

        $overlay_statuses = OverlayStatus::join('overlays','overlays.id','=','overlay_statuses.overlay_id')
            ->join('statuses','statuses.id','=','overlay_statuses.status_id')
            ->select("overlays.*","statuses.value as status_value","statuses.label as status_label")
            ->where('overlay_statuses.overlay_id',$overlay->id)
            ->get();

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail du dossier '.$uuid.'.',
                'overlay' => $overlay,
                'overlay_statuses' => $overlay_statuses,
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

        $this->validate($request, [
            'number' => 'required',
            'label' => 'required',
            'status_id' => 'required',
        ]);
        
        $overlay = Overlay::where('id', $request->id)->update(
            [
                'status_id' => $request->status_id,
                'updated_by' => $userId,
            ]
        );

        $overlay_exist = Overlay::where('id', $request->id)->first();

        $overlay_status = OverlayStatus::create(
            [
                'overlay_id' => $overlay_exist->id,
                'status_id' => $request->status_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        if($overlay && $overlay_status){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Recouvrement édité avec succès.', 
                    'overlay' => $overlay
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce dossier ne peut être édité en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

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
