<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Partner;
use App\Models\Repair;
use App\Models\Repairer;
use App\Models\Vehicle;
use App\Models\ShockPoint;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Register;
use App\Mail\ResetPassword;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->profil_id == 1 || $user->profil_id == 2){
            $users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                ->orderByDesc("users.created_at")
                ->paginate(10);
        } else {
            $users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                ->where("repairers.id", $user->repairer_id)
                ->orderByDesc("users.created_at")
                ->paginate(10);
        }


        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des utilisateurs.',
                'users' => $users
            ],
            201
        );
    }

    public function all()
    {
        $users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                ->where('users.status_id', 1)
                ->orderByDesc("users.created_at")
                ->get();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des utilisateurs.',
                'users' => $users
            ],
            201
        );
    }

    public function userProfile() {

        $user = Auth::user();
        $userId = $user->id;

        $user_logged = User::join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->select("users.*","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value","repairers.name as repairer_name")
                ->where("users.id",$userId)
                ->orderByDesc("users.created_at")
                ->first();

        $nb_repairs = Repair::join('vehicles','vehicles.id','=','repairs.vehicle_id')
                ->join('repairers','repairers.id','=','repairs.repairer_id')
                ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                ->join('users','users.id','=','repairs.created_by')
                ->join('statuses','statuses.id','=','repairs.status_id')
                ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.brand as vehicle_brand","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option","vehicles.color as vehicle_color",
                        "vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                        "shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","statuses.value as status_value","statuses.label as status_label")
                ->where("vehicles.license_plate",'!=', '0001EE01')
                ->orderByDesc("repairs.created_at")
                ->count();

        $nb_repairers = Repairer::join('users','users.id','=','repairers.created_by')
                ->join('statuses','statuses.id','=','repairers.status_id')
                ->select("repairers.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('repairers.status_id', 1)
                ->orderBy("repairers.created_at","DESC")
                ->count();

        $nb_vehicles = Vehicle::join('users','users.id','=','vehicles.created_by')
                ->join('statuses','statuses.id','=','vehicles.status_id')
                ->select("vehicles.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('vehicles.status_id', 1)
                ->orderByDesc("vehicles.created_at")
                ->count();

        $nb_shock_points = ShockPoint::join('users','users.id','=','shock_points.created_by')
                ->join('statuses','statuses.id','=','shock_points.status_id')
                ->select("shock_points.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('shock_points.status_id', 1)
                ->orderByDesc("shock_points.created_at")
                ->count();

        $nb_designations = Designation::join('users','users.id','=','designations.created_by')
                ->join('statuses','statuses.id','=','designations.status_id')
                ->select("designations.*","users.first_name as user_first_name","users.last_name as user_last_name","statuses.value as status_value","statuses.label as status_label")
                ->where('designations.status_id', 1)
                ->orderByDesc("designations.created_at")
                ->count();

        $nb_users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                // ->where('users.status_id', 1)
                ->orderByDesc("users.created_at")
                ->count();

        // $nb_assignments = Assignment::join('repairers','repairers.id','=','assignments.repairer_id')
        //         ->join('statuses','statuses.id','=','assignments.status_id')
        //         ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","repairers.name as repairer_name")
        //         ->where('assignments.etat',1)
        //         ->count();

        // $nb_assignment_non_traite = Assignment::join('repairers','repairers.id','=','assignments.repairer_id')
        //         ->join('statuses','statuses.id','=','assignments.status_id')
        //         ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","repairers.name as repairer_name")
        //         ->where('statuses.value',3)
        //         ->where('assignments.etat',1)
        //         ->orderByDesc("assignments.created_at")
        //         ->count();

        // $nb_assignment_en_attente_de_rapport = Assignment::join('repairers','repairers.id','=','assignments.repairer_id')
        //         ->join('statuses','statuses.id','=','assignments.status_id')
        //         ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","repairers.name as repairer_name")
        //         ->where('statuses.value',4)
        //         ->where('assignments.etat',1)
        //         ->orderByDesc("assignments.created_at")
        //         ->count();

        // $nb_assignment_traite = Assignment::join('repairers','repairers.id','=','assignments.repairer_id')
        //         ->join('statuses','statuses.id','=','assignments.status_id')
        //         ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","repairers.name as repairer_name")
        //         ->where('statuses.value',5)
        //         ->where('assignments.etat',1)
        //         ->orderByDesc("assignments.created_at")
        //         ->count();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Informations de l\'utilisateur connecté.',
                'user_logged' => $user_logged,
                'nb_repairs' => $nb_repairs,
                'nb_repairers' => $nb_repairers,
                'nb_vehicles' => $nb_vehicles,
                'nb_shock_points' => $nb_shock_points,
                'nb_designations' => $nb_designations,
                'nb_users' => $nb_users,
                // 'nb_assignments' => $nb_assignments,
                // 'nb_assignment_non_traite' => $nb_assignment_non_traite,
                // 'nb_assignment_en_attente_de_rapport' => $nb_assignment_en_attente_de_rapport,
                // 'nb_assignment_traite' => $nb_assignment_traite,
            ],
            201
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function randomPassword() {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&?';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $repairer_id = $user->repairer_id;

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $exist = User::select("*")
            ->where('email','=', $request->email)
            ->count();

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } else{

            $this->validate($request, [
                'first_name' => 'required',
                'email' => 'required|email',
                'profil_id' => 'required',
                'repairer_id' => 'required',
            ]);

            // $password = $this->randomPassword();
            $password = "12345678";

            $signature = '';

            if($request->file('signature') && $request->hasfile('signature')){
                $signature_path = $request->file('signature');
                $signature_name = 'IMG'.$today.'.'.$signature_path->getClientOriginalExtension();
                $signature_path->move(public_path('storage/signature'), $signature_name);
                $signature = $signature_name;

                $user = User::create(
                    [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'repairer_id' => $repairer_id,
                        'profil_id' => $request->profil_id,
                        'signature' => $signature,
                        'status_id' => 1,
                        'password' => Hash::make($password),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );

                if($user){

                    $email = $user->email;

                    Mail::to($email)->cc('brahimafane06@gmail.com')->send(new Register($email,$password));

                    return new JsonResponse(
                        [
                            'success' => true,
                            'message' => 'Utilisateur enregistré avec succès.',
                            'user' => $user
                        ],
                        201
                    );

                } else {

                    return new JsonResponse(
                        [
                            'success' => false,
                            'message' => 'Cet utilisateur ne peut être ajouté en ce moment, veuillez réessayer plus tard !',
                        ],
                        406
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

    }

    public function create_by_repairer(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $repairer_id = $request->repairer_id;
        if($user->profil_id == 3){
            $repairer_id = $user->repairer_id;
        }

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $exist = User::select("*")
            ->where('email','=', $request->email)
            ->count();

        if($exist > 0){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } else{

            $this->validate($request, [
                'first_name' => 'required',
                'email' => 'required|email',
                'profil_id' => 'required',
                'repairer_id' => 'required',
            ]);

            // $password = $this->randomPassword();
            $password = "12345678";

            $user = User::create(
                [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'repairer_id' => $repairer_id,
                    'profil_id' => $request->profil_id,
                    'status_id' => 1,
                    'password' => Hash::make($password),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            if($user){

                $email = $user->email;

                Mail::to($email)->cc('brahimafane06@gmail.com')->send(new Register($email,$password));

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Utilisateur enregistré avec succès.',
                        'user' => $user
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être ajouté en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
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
    public function show($uuid)
    {
        $user = User::join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","profils.label","statuses.label")
                ->where('uuid',$uuid)
                ->first();

        return new JsonResponse(
            [
                'success' => true,
                'message' => "Détail de l'utilisateur '.$id.'.",
                'user' => $user,
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

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $exist = User::select("*")
            ->where('email','=', $request->email)
            ->count();

        $exist_element = User::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } elseif ($exist == 1 && $exist_element->email != $request->email) {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } else{

            $this->validate($request, [
                'first_name' => 'required',
                'email' => 'required|email',
                'profil_id' => 'required',
                'repairer_id' => 'required',
            ]);

            $signature = '';

            if($request->password == null){

                if($request->file('signature') && $request->hasfile('signature')){
                    $signature_path = $request->file('signature');
                    $signature_name = 'IMG'.$today.'.'.$signature_path->getClientOriginalExtension();
                    $signature_path->move(public_path('storage/signature'), $signature_name);
                    $signature = $signature_name;

                    $data = [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'repairer_id' => $request->repairer_id,
                        'profil_id' => $request->profil_id,
                        'signature' => $signature,
                        'updated_by' => $userId,
                    ];
                } else {
                    $data = [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'repairer_id' => $request->repairer_id,
                        'profil_id' => $request->profil_id,
                        'updated_by' => $userId,
                    ];
                }


            }else{

                if($request->file('signature') && $request->hasfile('signature')){
                    $signature_path = $request->file('signature');
                    $signature_name = 'IMG'.$today.'.'.$signature_path->getClientOriginalExtension();
                    $signature_path->move(public_path('storage/signature'), $signature_name);
                    $signature = $signature_name;

                    $data = [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'repairer_id' => $request->repairer_id,
                        'profil_id' => $request->profil_id,
                        'signature' => $signature,
                        'password' => Hash::make($request->password),
                        'updated_by' => $userId,
                    ];
                } else {
                    $data = [
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'repairer_id' => $request->repairer_id,
                        'profil_id' => $request->profil_id,
                        'password' => Hash::make($request->password),
                        'updated_by' => $userId,
                    ];
                }

            };

            $user = User::where('id', $request->id)->update($data);

            if($user){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Utilisateur mis à jour avec succès.',
                        'user' => $user
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
                );

            }

        }


    }

    public function update_by_repairer(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $exist = User::select("*")
            ->where('email','=', $request->email)
            ->count();

        $exist_element = User::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist > 1){

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } elseif ($exist == 1 && $exist_element->email != $request->email) {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur existe déjà !',
                ],
                406
            );

        } else{

            $this->validate($request, [
                'first_name' => 'required',
                'email' => 'required|email',
                'profil_id' => 'required',
                'repairer_id' => 'required',
            ]);

            if($request->password == null){

                $data = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'repairer_id' => $request->repairer_id,
                    'profil_id' => $request->profil_id,
                    'updated_by' => $userId,
                ];

            }else{

                $data = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'repairer_id' => $request->repairer_id,
                    'profil_id' => $request->profil_id,
                    'password' => Hash::make($request->password),
                    'updated_by' => $userId,
                ];

            };

            $user = User::where('id', $request->id)->update($data);

            if($user){

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Utilisateur mis à jour avec succès.',
                        'user' => $user
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
                );

            }

        }


    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $user = User::where('id', $request->id)->update(
            [
                'status_id' => 1,
                'updated_by' => $userId,
            ]
        );

        if($user){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès.',
                    'user' => $user
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                406
            );

        }
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $user = User::where('id', $request->id)->update(
            [
                'status_id' => 2,
                'updated_by' => $userId,
            ]
        );

        if($user){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès.',
                    'user' => $user
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                ],
                406
            );

        }
    }

    public function reset(Request $request)
    {
        $exist_element = User::select("*")
            ->where('id',$request->id)
            ->first();

        if($exist_element){
            // $password = $this->randomPassword();
            $password = "12345678";

            $user = User::where('id', $request->id)->update([
                'password' => Hash::make($password),
            ]);

            if($user){

                $email = $exist_element->email;

                Mail::to($email)->cc('brahimafane06@gmail.com')->send(new ResetPassword($email,$password));

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Compte réinitialisé avec succès.',
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
                );

            }
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé !',
                ],
                404
            );
        }

    }

    public function reset_password(Request $request)
    {
        $exist_element = User::select("*")
            ->where('email',$request->email)
            ->first();

        if($exist_element){
            $password = $this->randomPassword();

            $user = User::where('email', $request->email)->update([
                'password' => Hash::make($password),
            ]);

            if($user){

                $email = $request->email;

                Mail::to($email)->send(new ResetPassword($email,$password));

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Compte réinitialisé avec succès.',
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
                );

            }
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé !',
                ],
                404
            );
        }

    }

    public function reset_user_password(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $user = User::join('profils', 'profils.id', '=', 'users.profil_id')
                    ->join('statuses', 'statuses.id', '=', 'users.status_id')
                    ->select("users.*","profils.label as profil_label","profils.value as profil_value","statuses.label as status_label","statuses.value as status_value")
                    ->where('email', $request->email)
                    ->where('users.status_id', 1)
                    ->first();

        // Check Password
        if ($user && Hash::check($request->password, $user->password)) {

            if($request->file('signature') && $request->hasfile('signature')){
                $signature_path = $request->file('signature');
                $signature_name = 'IMG'.$today.'.'.$signature_path->getClientOriginalExtension();
                $signature_path->move(public_path('storage/signature'), $signature_name);
                $signature = $signature_name;

                // $extension = $signature_path->getClientOriginalExtension();
                // switch ($extension) {
                //     case 'jpg':
                //     case 'jpeg':
                //     $image = imagecreatefromjpeg(public_path('storage/signature/'.$signature_name));
                //     break;
                //     case 'gif':
                //     $image = imagecreatefromgif(public_path('storage/signature/'.$signature_name));
                //     break;
                //     case 'png':
                //     $image = imagecreatefrompng(public_path('storage/signature/'.$signature_name));
                //     break;
                // }

                // $white = imagecolorallocate($image, 255, 255, 255);
                // imagecolortransparent($image, $white);
                // imagepng($image, $_SERVER['DOCUMENT_ROOT'].'/test.png');

                // // create new manager instance with desired driver and default blending color
                // $manager = new ImageManager(Driver::class);

                // // read image from file system
                // $image = $manager->read($_SERVER['DOCUMENT_ROOT'].'/test.png');

                // $image->pad(300, 200, 'transparent');

                $data = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'signature' => $signature,
                    'password' => Hash::make($request->password),
                    'updated_by' => $userId,
                ];
            } else {
                $data = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'updated_by' => $userId,
                ];
            }

            $userUpdated = User::where('id', $request->id)->update($data);

            if($userUpdated){

                // $email = $request->email;
                // $password = $request->password;

                //Mail::to($email)->send(new ResetPassword($email,$password));

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => 'Compte réinitialisé avec succès.',
                    ],
                    201
                );

            } else {

                return new JsonResponse(
                    [
                        'success' => false,
                        'message' => 'Cet utilisateur ne peut être mis à jour en ce moment, veuillez réessayer plus tard !',
                    ],
                    406
                );

            }
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Utilisateur introuvable !'
                ],
                400
            );
        }

    }

    public function last_operation(Request $request)
    {

        $now = Carbon::now();

        $data = [
            'last_operation_at' => $now,
        ];

        $user = User::where('id', $request->id)->update($data);

        if($user){

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Utilisateur enregistré avec succès.',
                    'user' => $user
                ],
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Cet utilisateur ne peut être ajouté en ce moment, veuillez réessayer plus tard !',
                ],
                400
            );

        }

    }

    public function search($information)
    {
        $user = Auth::user();

        if($user->profil_id == 1 || $user->profil_id == 2){
            $users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                ->where('users.email','like', '%'.$information.'%')
                ->orWhere('users.first_name','like', '%'.$information.'%')
                ->orWhere('users.last_name','like', '%'.$information.'%')
                ->orderByDesc("users.created_at")
                ->paginate(10);
        } else {
            $users = User::join('repairers', 'repairers.id', '=', 'users.repairer_id')
                ->join('profils', 'profils.id', '=', 'users.profil_id')
                ->join('statuses', 'statuses.id', '=', 'users.status_id')
                ->select("users.*","repairers.name as repairer_name","profils.value as profil_value","profils.label as profil_label","statuses.label","statuses.label as status_label","statuses.value as status_value")
                ->where("repairers.id", $user->repairer_id)
                ->where('users.email','like', '%'.$information.'%')
                // ->orWhere('users.first_name','like', '%'.$information.'%')
                // ->orWhere('users.last_name','like', '%'.$information.'%')
                ->orderByDesc("users.created_at")
                ->paginate(10);
        }

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des utilisateurs.',
                'users' => $users
            ],
            201
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
