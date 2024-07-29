<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use NumberToWords\NumberToWords;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Partner;
use App\Models\Assignment;
use App\Models\AssignmentStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignments = Assignment::join('partners','partners.id','=','assignments.partner_id')
                    ->join('statuses','statuses.id','=','assignments.status_id')
                    ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","partners.name as partner_name")
                    ->where('assignments.etat',1)
                    ->orderByDesc("assignments.created_at")
                    ->get();

        // $user = Auth::user();
        // $userId = $user->id;

        // $partenaire = \DB::table('partenaires')
        //             ->select("partenaires.*")
        //             //->take(1)
        //             ->get();

        // foreach($partenaire as $item){

        //     $partner = Partner::create(
        //         [
        //             'name' => $item->PARTENAIRE,
        //             'etat' => 1,
        //             'created_by' => $userId,
        //             'updated_by' => $userId,
        //             'status_id' => 1,
        //         ]
        //     );

        //     $user = User::create(
        //         [
        //             'first_name' => $item->PARTENAIRE,
        //             //'last_name' => $item->PRENOM,
        //             'email' => $item->EMAIL,
        //             //'phone' => $item->CONTACT,
        //             'partner_id' => $partner->id,
        //             'profil_id' => 4,   
        //             'status_id' => 1,
        //             'password' => Hash::make($item->MOT_DE_PASSE),
        //             'created_by' => $userId,
        //             'updated_by' => $userId,
        //         ]
        //     );

        // }

                        

        

        //$user = User::find($userId);
        //$token = $user->createToken('myapptoken')->plainTextToken;
        //$a = $user->name;
        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des missions.', 
                'assignments' => $assignments
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
        $assignment_number = 'MIS'.$today;

        $partner = Partner::where('partners.id',$userPartnerId)
            ->first();

        $this->validate($request, [
            'client_first_name' => 'required',
            'client_phone' => 'required',
            'policy_number' => 'required',
            'disaster_date' => 'required|date|before_or_equal:today',
            //'expertise_date' => 'required',
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

        $assignment = Assignment::create(
            [
                'number' => $assignment_number,
                'client_first_name' => $request->client_first_name,
                'client_last_name' => $request->client_last_name,
                'client_phone' => $request->client_phone,
                'policy_number' => $request->policy_number,
                'disaster_number' => $request->disaster_number,
                'disaster_date' => $request->disaster_date,
                'expertise_date' => $request->expertise_date,
                'expertise_address' => $partner->address,
                'pictures' => json_encode($files),
                'partner_id' => $request->partner_id,
                'status_id' => 3,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $assignment_status = AssignmentStatus::create(
            [
                'assignment_id' => $assignment->id,
                'status_id' => 3,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );


        if($assignment && $assignment_status){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mission enregistrée avec succès.', 
                    'assignment' => $assignment
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette mission ne peut être enregistrée en ce moment, veuillez réessayer plus tard !', 
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
        $assignment = Assignment::join('partners','partners.id','=','assignments.partner_id')
            ->join('statuses','statuses.id','=','assignments.status_id')
            ->select("assignments.*","statuses.value as status_value","statuses.label as status_label","partners.name as partner_name")
            ->where('assignments.uuid',$uuid)
            ->first();

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail de la mission '.$uuid.'.',
                'assignment' => $assignment,
                'pictures' => json_decode($assignment->pictures)
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
            'car_immatriculation' => 'required',
            'car_brand' => 'required',
            'car_model' => 'required',
            'car_color' => 'required',
            'car_nb_place' => 'required',
            'car_fiscal_powerful' => 'required',
            'car_serial_number' => 'required',
            'car_gender' => 'required',
            'car_km_comptor' => 'required',
            'car_general_state' => 'required',
            'car_new_value' => 'required',
            'car_depreciation' => 'required',
            'car_market_value' => 'required',
            'car_first_circulation_date' => 'required|date|before_or_equal:today',
        ]);
        
        $assignment = Assignment::where('id', $request->id)->update(
            [
                'car_immatriculation' => $request->car_immatriculation,
                'car_brand' => $request->car_brand,
                'car_model' => $request->car_model,
                'car_color' => $request->car_color,
                'car_nb_place' => $request->car_nb_place,
                'car_fiscal_powerful' => $request->car_fiscal_powerful,
                'car_serial_number' => $request->car_serial_number,
                'car_serial_number' => $request->car_serial_number,
                'car_gender' => $request->car_gender,
                'car_km_comptor' => $request->car_km_comptor,
                'car_general_state' => $request->car_general_state,
                'car_new_value' => $request->car_new_value,
                'car_depreciation' => $request->car_depreciation,
                'car_market_value' => $request->car_market_value,
                'car_first_circulation_date' => $request->car_first_circulation_date,
                'status_id' => 4,
                'updated_by' => $userId,
            ]
        );

        $assignment_exist = Assignment::where('id', $request->id)->first();

        $assignment_updated = Assignment::where('id', $request->id)->update(
            [
                'file' => $assignment_exist->number.".pdf",
            ]
        );

        $assignment_status = AssignmentStatus::create(
            [
                'assignment_id' => $assignment_exist->id,
                'status_id' => 4,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $path = base_path('public/images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $pdf = PDF::loadView('assignment/index',compact('assignment_exist','logo'));
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path("assignment/files/".$assignment_exist->number.".pdf"));
        $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

        if($assignment && $assignment_status){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Mission éditée avec succès.', 
                    'assignment' => $assignment
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Cette mission ne peut être éditée en ce moment, veuillez réessayer plus tard !', 
                ], 
                400
            );

        }

    }

    public function add_report(Request $request)
    {
        $annee = date("Y");
        $mois_jour_heure = date("mdh");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        
        $user = Auth::user();
        $userId = $user->id;

        $assignment_exist = Assignment::where('id', $request->id)->first();

        $report = '';
        if($request->hasfile('report')){
            $report_path = $request->file('report');
            $report_name = $assignment_exist->number.'.'.$report_path->getClientOriginalExtension();
            $report_path->move(public_path('assignment/reports/'), $report_name);
            $report = $report_name; 

            $assignment = Assignment::where('id', $request->id)->update(
                [
                    'report' => $report,
                    'status_id' => 5,
                    'updated_by' => $userId,
                ]
            );

            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => "Rapport d'expertise ajouté avec succès !",
                    "report"=> $report,
                ], 
                201
            );

        } 
        else
        {
            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => "Veuillez sélectionner un fichier !",
                ], 
                401
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
