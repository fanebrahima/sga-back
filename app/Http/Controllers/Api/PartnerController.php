<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::select("*")
                ->orderBy("partners.created_at","DESC")
                ->paginate(10);        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des partenaires.', 
                'partners' => $partners
            ], 
            200
        );
    }

    public function all()
    {
        $partners = Partner::select("*")
                ->orderBy("partners.created_at","DESC")
                ->get();        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des partenaires.', 
                'partners' => $partners
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

        $exist = Partner::select("*")
            ->where('name', 'like', $request->name)
            ->count();  

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce partenaire existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'name' => 'required',
            ]);
    
            $partner = Partner::create(
                [
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'responsible_first_name' => $request->responsible_first_name,
                    'responsible_last_name' => $request->responsible_last_name,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );  
    
            if($partner){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Partenaire enregistré avec succès.', 
                        'partner' => $partner
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce partenaire ne peut être ajouté en ce moment, veuillez réessayer plus tard !', 
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
        $partner = Partner::select('*')
                ->where('id',$id)
                ->find($id);

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail du partenaire '.$id.'.',
                'partner' => $partner,
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

        $exist = Partner::select("*")
            ->where('name', 'like', $request->name)
            ->count();

        $exist_element = Partner::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce partenaire existe déjà !', 
                ], 
                201
            );

        } elseif ($exist == 1 && $exist_element->name != $request->name) {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce partenaire existe déjà !', 
                ], 
                201
            );

        } else{

            $this->validate($request, [
                'name' => 'required',
            ]);
            
            $partner = Partner::where('id', $request->id)->update(
                [
                    'name' => $request->name,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'responsible_first_name' => $request->responsible_first_name,
                    'responsible_last_name' => $request->responsible_last_name,
                    'updated_by' => $userId,
                ]
            );

            if($partner){
    
                return new JsonResponse(
                    [
                        'success' => true, 
                        'message' => 'Mise à jour du taux avec succès.', 
                        'partner' => $partner
                    ], 
                    201
                );
    
            } else {
    
                return new JsonResponse(
                    [
                        'success' => false, 
                        'message' => 'Ce partenaire ne peut être mis à jour en ce moment, veuillez réessayer plus tard !', 
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
