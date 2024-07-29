<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\VoucherType;
use App\Http\Resources\RateResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VoucherTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $voucher_types = VoucherType::select("*")
                    ->orderByDesc("voucher_types.created_at")
                    ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des types de reçu.', 
                'voucher_types' => $voucher_types
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

        $exist = VoucherType::select("*")
            ->orWhere('label', 'like', $request->label)
            ->count();  

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce type existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
    
            $voucher_type = VoucherType::create(
                [
                    'label' => $request->label,
                    'value' => $request->value,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );  
    
            if($voucher_type){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Type de reçu enregistré avec succès.', 
                        'vou$voucher_type' => $voucher_type
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce type de reçu ne peut être ajouté en ce moment, veuillez réessayer plus tard !', 
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
        $voucher_type = VoucherType::select('*')
                ->where('id',$id)
                ->find($id);

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail du type de reçu '.$id.'.',
                'voucher_type' => $voucher_type,
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

        $exist = VoucherType::select("*")
            ->orWhere('label', 'like', $request->label)
            ->count();

        $exist_element = VoucherType::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce type de reçu existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->label != $request->label) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce type de reçu existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'label' => 'required',
            ]);
            
            $voucher_type = VoucherType::where('id', $request->id)->update(
                [
                    'label' => $request->label,
                    'description' => $request->description,
                    'updated_by' => $userId,
                ]
            );

            if($voucher_type){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du type de reçu avec succès.', 
                        'voucher_type' => $voucher_type
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce type de reçu ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
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