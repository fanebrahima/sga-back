<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repair;
use App\Models\QrCode;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class QrCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $qr_codes = QrCode::join('users','users.id','=','qr_codes.created_by')
                    ->join('statuses','statuses.id','=','qr_codes.status_id')
                    ->select("qr_codes.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("qr_codes.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des qr codes.', 
                'qr_codes' => $qr_codes,
            ], 
            200
        );
    }

    public function one()
    {

        $qr_code = QrCode::join('users','users.id','=','qr_codes.created_by')
                    ->join('statuses','statuses.id','=','qr_codes.status_id')
                    ->select("qr_codes.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('qr_codes.status_id',1)
                    ->first();   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des qr codes.', 
                'qr_code' => $qr_code,
            ], 
            200
        );
    }

    public function all()
    {
        $qr_codes = QrCode::join('users','users.id','=','qr_codes.created_by')
                    ->join('statuses','statuses.id','=','qr_codes.status_id')
                    ->select("qr_codes.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('qr_codes.status_id', 1)
                    ->orderByDesc("qr_codes.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des qr codes.', 
                'qr_codes' => $qr_codes
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

        if($request->file('qr_code') && $request->hasfile('qr_code')){
            $path = $request->file('qr_code');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            QrCode::query()->update([
                'status_id' => 2,
            ]);

            $qr_code = QrCode::create(
                [
                    'code' => $request->code,
                    'qr_code' => $base64,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            if($qr_code){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Qr code enregistrée avec succès.', 
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
        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "Veuillez sélectionner une image pour la signature !",
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
    public function show($id)
    {
        $qr_code = QrCode::join('users','users.id','=','qr_codes.created_by')
                    ->join('statuses','statuses.id','=','qr_codes.status_id')
                    ->select("qr_codes.*","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("qr_codes.created_at")
                    ->where('qr_codes.id',$id)
                    ->first();           

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des qr codes.', 
                'qr_code' => $qr_code
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

    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        QrCode::query()->update([
            'status_id' => 2,
        ]);

        $qr_code = QrCode::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($qr_code){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la marque avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette marque ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $qr_code = QrCode::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($qr_code){

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mise à jour de la marque avec succès.', 
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette marque ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function search($information)
    {

        $qr_codes = QrCode::join('users','users.id','=','qr_codes.created_by')
                    ->join('statuses','statuses.id','=','qr_codes.status_id')
                    ->select("qr_codes.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                    ->where('qr_codes.label','like', '%'.$information.'%')
                    ->orderByDesc("qr_codes.created_at")
                    ->paginate(10);   

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des qr codes.', 
                'qr_codes' => $qr_codes,
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