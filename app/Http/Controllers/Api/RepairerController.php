<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Repairer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RepairerController extends Controller
{
    public function index()
    {
        $repairers = Repairer::join('users','users.id','=','repairers.created_by')
                ->join('statuses','statuses.id','=','repairers.status_id')
                ->select("repairers.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->orderBy("repairers.created_at","DESC")
                ->paginate(10);

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparateurs.',
                'repairers' => $repairers
            ],
            200
        );
    }

    public function all()
    {
        $repairers = Repairer::join('users','users.id','=','repairers.created_by')
                ->join('statuses','statuses.id','=','repairers.status_id')
                ->select("repairers.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('repairers.status_id', 1)
                // ->where("repairers.id",'!=', 1)
                ->orderBy("repairers.created_at","DESC")
                ->get();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparateurs.',
                'repairers' => $repairers
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

        $exist = Repairer::select("*")
            ->where('name', 'like', $request->name)
            ->count();

        if($exist > 0){

            $repairer = Repairer::select("*")
                ->where('name', 'like', $request->name)
                ->first();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Ce réparateur existe déjà !',
                    'repairer' => $repairer
                ],
                201
            );

        } else{

            $this->validate($request, [
                'name' => 'required',
            ]);

            $name = $request->name;
            $phone = $request->phone;
            $email = $request->email;

            if($request->name == 'null'){
                $name = "";
            }
            if($request->phone == 'null'){
                $phone = "";
            }
            if($request->email == 'null'){
                $email = "";
            }

            $repairer = Repairer::create(
                [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $request->address,
                    'responsible_first_name' => $request->responsible_first_name,
                    'responsible_last_name' => $request->responsible_last_name,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            if($repairer){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Réparateur enregistré avec succès.',
                        'repairer' => $repairer
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Ce réparateur ne peut être ajouté en ce moment, veuillez réessayer plus tard !',
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
        $repairer = Repairer::join('users','users.id','=','repairers.created_by')
                ->join('statuses','statuses.id','=','repairers.status_id')
                ->select("repairers.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->orderBy("repairers.created_at","DESC")
                ->where('repairers.id',$id)
                ->first();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparateurs.',
                'repairer' => $repairer
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

        $exist = Repairer::select("*")
            ->where('name', 'like', $request->name)
            ->count();

        $exist_element = Repairer::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce réparateur existe déjà !',
                ],
                201
            );

        } elseif ($exist == 1 && $exist_element->name != $request->name) {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce réparateur existe déjà !',
                ],
                201
            );

        } else{

            $this->validate($request, [
                'name' => 'required',
            ]);

            $repairer = Repairer::where('id', $request->id)->update(
                [
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'responsible_first_name' => $request->responsible_first_name,
                    'responsible_last_name' => $request->responsible_last_name,
                    'updated_by' => $userId,
                ]
            );

            if($repairer){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Mise à jour du réparateur avec succès.',
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Ce réparateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    201
                );

            }
        }

    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $repairer = Repairer::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($repairer){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Mise à jour du réparateur avec succès.',
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce réparateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                201
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $repairer = Repairer::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($repairer){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Mise à jour du réparateur avec succès.',
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce réparateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                201
            );

        }

    }

    public function search($information)
    {
        $repairers = Repairer::join('users','users.id','=','repairers.created_by')
                ->join('statuses','statuses.id','=','repairers.status_id')
                ->select("repairers.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('repairers.name','like', '%'.$information.'%')
                ->orderBy("repairers.created_at","DESC")
                ->paginate(10);

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparateurs.',
                'repairers' => $repairers
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
