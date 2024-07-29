<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::join('users','users.id','=','clients.created_by')
                ->join('statuses','statuses.id','=','clients.status_id')
                ->select("clients.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->orderBy("clients.created_at","DESC")
                ->paginate(10);

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des clients.',
                'clients' => $clients
            ],
            200
        );
    }

    public function all()
    {
        $clients = Client::join('users','users.id','=','clients.created_by')
                ->join('statuses','statuses.id','=','clients.status_id')
                ->select("clients.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('clients.status_id', 1)
                ->orderBy("clients.created_at","DESC")
                ->get();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des clients.',
                'clients' => $clients
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

        $exist = Client::select("*")
            ->where('name', 'like', $request->name)
            ->count();

        if($exist > 0){

            $client = Client::select("*")
                ->where('name', 'like', $request->name)
                ->first();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Ce client existe déjà !',
                    'client' => $client
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

            $client = Client::create(
                [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $request->address,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            if($client){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Client enregistré avec succès.',
                        'client' => $client
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Ce client ne peut être ajouté en ce moment, veuillez réessayer plus tard !',
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
        $client = Client::join('users','users.id','=','clients.created_by')
                ->join('statuses','statuses.id','=','clients.status_id')
                ->select("clients.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->orderBy("clients.created_at","DESC")
                ->where('clients.id',$id)
                ->first();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des clients.',
                'client' => $client
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

        $exist = Client::select("*")
            ->where('name', 'like', $request->name)
            ->count();

        $exist_element = Client::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce client existe déjà !',
                ],
                201
            );

        } elseif ($exist == 1 && $exist_element->name != $request->name) {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce client existe déjà !',
                ],
                201
            );

        } else{

            $this->validate($request, [
                'name' => 'required',
            ]);

            $client = Client::where('id', $request->id)->update(
                [
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'updated_by' => $userId,
                ]
            );

            if($client){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Mise à jour du client avec succès.',
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Ce client ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
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

        $client = Client::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($client){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Mise à jour du client avec succès.',
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce client ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                201
            );

        }

    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $client = Client::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($client){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Mise à jour du client avec succès.',
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Ce client ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                201
            );

        }

    }

    public function search($information)
    {
        $clients = Client::join('users','users.id','=','clients.created_by')
                ->join('statuses','statuses.id','=','clients.status_id')
                ->select("clients.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('clients.name','like', '%'.$information.'%')
                ->orderBy("clients.created_at","DESC")
                ->paginate(10);

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des clients.',
                'clients' => $clients
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
