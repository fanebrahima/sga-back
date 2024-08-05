<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use NumberToWords\NumberToWords;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Repairer;
use App\Models\Shock;
use App\Models\ShockPoint;
use App\Models\Client;
use App\Models\Insurer;
use App\Models\QrCode;
use App\Models\Repair;
use App\Models\RepairWork;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendMail;

class RepairController extends Controller
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
            $repairs = Repair::with('repairer','vehicle','insurer','client','user','shocks','status')
                ->orderByDesc("repairs.created_at")
                ->paginate(10);
                
            
        } else {
            $repairs = Repair::with('repairer','vehicle','insurer','client','user','shocks','status')
                ->where("repairers.id", $user->repairer_id)
                ->orderByDesc("repairs.created_at")
                ->paginate(10);
        }

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparations.',
                'repairs' => $repairs
            ],
            200
        );
    }

    public function all()
    {
        $user = Auth::user();

        if($user->profil_id == 1 || $user->profil_id == 2){
            $repairs = Repair::with('insurer','client')
                    ->join('vehicles','vehicles.id','=','repairs.vehicle_id')
                    ->join('repairers','repairers.id','=','repairs.repairer_id')
                    ->join('clients','clients.id','=','repairs.client_id')
                    ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                    ->join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('users','users.id','=','repairs.created_by')
                    ->join('statuses','statuses.id','=','repairs.status_id')
                    ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                            "brands.label as brand_label","colors.label as color_label","vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                            "clients.name as client_name","clients.address as client_address","clients.phone as client_phone","clients.email as client_email","shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                    ->where("vehicles.license_plate",'!=', '0001EE01')
                    ->orderByDesc("repairs.created_at")
                    ->get();
        } else {
            $repairs = Repair::with('insurer','client')
                    ->join('vehicles','vehicles.id','=','repairs.vehicle_id')
                    ->join('repairers','repairers.id','=','repairs.repairer_id')
                    ->join('clients','clients.id','=','repairs.client_id')
                    ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                    ->join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('users','users.id','=','repairs.created_by')
                    ->join('statuses','statuses.id','=','repairs.status_id')
                    ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                            "brands.label as brand_label","colors.label as color_label","vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                            "clients.name as client_name","clients.address as client_address","clients.phone as client_phone","clients.email as client_email","shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                    ->where("repairers.id", $user->repairer_id)
                    ->where("vehicles.license_plate",'!=', '0001EE01')
                    ->orderByDesc("repairs.created_at")
                    ->get();
        }

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparations.',
                'repairs' => $repairs,
            ],
            200
        );
    }

    public function all_remark()
    {
        $repair_remarks = Repair::select("repairs.remark")
                    ->distinct("repairs.remark")
                    ->orderBy("repairs.remark","ASC")
                    ->get();

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des notes.',
                'repair_remarks' => $repair_remarks
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
        $user_signature = $user->signature;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $exist_repair = Repair::where('reference',$reference)->first();

        if($exist_repair){
            $reference = $reference.'_'.$userId;
        }

        $path = base_path('public/images/logo_eg.jpg');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $path_check_icon = base_path('public/images/check-icon.png');
        $type_check_icon = pathinfo($path_check_icon, PATHINFO_EXTENSION);
        $data_check_icon = file_get_contents($path_check_icon);
        $check_icon = 'data:image/'.$type_check_icon.';base64,'.base64_encode($data_check_icon);

        #$path_signature = public_path('storage/signature/'.$user_signature);
        #$type_signature = pathinfo($path_signature, PATHINFO_EXTENSION);
        #$data_signature = file_get_contents($path_signature);
        #$signature = 'data:image/'.$type_signature.';base64,'.base64_encode($data_signature);

        $path_wbg = base_path('public/images/wbg.png');
        $type_wbg = pathinfo($path_wbg, PATHINFO_EXTENSION);
        $data_wbg = file_get_contents($path_wbg);
        $wbg = 'data:image/'.$type_wbg.';base64,'.base64_encode($data_wbg);

        $exist_client = Client::where('name', 'like', '%'.$request->name.'%')->first();
        $exist_insurer = Insurer::where('name', 'like', '%'.$request->name.'%')->first();

        if($exist_client){
            $client = $exist_client;
        } else {
            $client = Client::create(
                [
                    'name' => $request->client_name,
                    'phone' => $request->client_phone,
                    'email' => $request->client_email,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        if($exist_insurer){
            $insurer = $exist_insurer;
        } else {
            $insurer = Insurer::create(
                [
                    'name' => $request->insurer_name,
                    'phone' => $request->insurer_phone,
                    'email' => $request->insurer_email,
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        $repair = Repair::create(
            [
                'reference' => $reference,
                'repairer_id' => $request->repairer_id,
                'client_id' => $client->id,
                'insurer_id' => $insurer->id,
                'vehicle_id' => $request->vehicle_id,
                // 'shock_point_id' => $request->shock_point_id,
                'disaster_number' => $request->disaster_number,
                'remark' => $request->remark,
                'amount' => $request->amount,
                'emails' => json_encode($request->emails),
                'expert_signature' => $request->expert_signature,
                'repairer_signature' => $request->repairer_signature,
                'customer_signature' => $request->customer_signature,
                'status_id' => 3,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $shock_points = $request->get('shock_points');

        foreach ($shock_points as $item) {

            $shock = Shock::create(
                [
                    'repair_id' => $repair->id,
                    'shock_point_id' => $item['shock_point_id'],
                    'status_id' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            foreach ($item['works'] as $data) {

                $repair_work = RepairWork::create(
                    [
                        'shock_id' => $shock->id,
                        'designation_id' => $data['designation_id'],
                        'replacement' => $data['replacement'],
                        'repair' => $data['repair'],
                        'paint' => $data['paint'],
                        'control' => $data['control'],
                        'status_id' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
    
            }

        }

        $repair = Repair::with('repairer','vehicle','insurer','client','user','shocks')
                ->select("*")
                ->where('repairs.id',$repair->id)
                ->first();

        $qr_code = QrCode::where('status_id', 1)->first();

        $repair_works = RepairWork::join('repairs','repairs.id','=','repair_works.repair_id')
                    ->join('designations','designations.id','=','repair_works.designation_id')
                    ->join('statuses','statuses.id','=','repair_works.status_id')
                    ->select("repair_works.*","designations.label as designation_label","statuses.value as status_value","statuses.label as status_label")
                    ->where("repair_works.repair_id",$repair->id)
                    ->get();

        $repairer = Repairer::select("*")
                ->orderBy("repairers.created_at","DESC")
                ->where('repairers.id',$request->repairer_id)
                ->first();

        $client = Client::select("*")
                ->orderBy("clients.created_at","DESC")
                ->where('clients.id',$client->id)
                ->first();

        $vehicle = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                ->join('colors','colors.id','=','vehicles.color_id')
                ->join('statuses','statuses.id','=','vehicles.status_id')
                ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","statuses.value as status_value","statuses.label as status_label")
                ->orderByDesc("vehicles.created_at")
                ->where('vehicles.id',$request->vehicle_id)
                ->first();

        $shock_point = ShockPoint::join('users','users.id','=','shock_points.created_by')
                ->join('statuses','statuses.id','=','shock_points.status_id')
                ->select("shock_points.*","statuses.value as status_value","statuses.label as status_label")
                ->orderByDesc("shock_points.created_at")
                ->where('shock_points.id',$request->shock_point_id)
                ->first();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        $email = 'brahimafane06@gmail.com';

        if($repair){

            $pdf = PDF::loadView('repair/index',compact('repair','qr_code','now','numberTransformer','logo','check_icon','wbg'));
            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->setOptions(['defaultFont' => 'sans-serif']);
            $pdf->save(public_path("storage/repair/".$repair->reference.".pdf"));
            $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

            $file = public_path("storage/repair/".$repair->reference.".pdf");

            $emails = $request->get('emails');
            $nb_email = count($emails);

            if($file &&  $nb_email > 0){
                Mail::to($emails)->cc('brahimafane06@gmail.com')->send(new SendMail($file,$repair->reference));
            }

            if (Mail::failures()) {
                $resp = "NOKNOH";
            } else {
                $resp = "OKOKOK";
            }

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Réparation enregistrée avec succès.',
                    'repair' => $repair,
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

    public function replay(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $user_signature = $user->signature;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $repair = Repair::join('vehicles','vehicles.id','=','repairs.vehicle_id')
                ->join('repairers','repairers.id','=','repairs.repairer_id')
                ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                ->join('users','users.id','=','repairs.created_by')
                ->join('statuses','statuses.id','=','repairs.status_id')
                ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                        "vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                        "shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                ->where('repairs.id', $request->id)
                ->first();

        $qr_code = QrCode::where('status_id', 1)->first();
        
        $repair_works = RepairWork::join('repairs','repairs.id','=','repair_works.repair_id')
        ->join('designations','designations.id','=','repair_works.designation_id')
        ->join('statuses','statuses.id','=','repair_works.status_id')
        ->select("repair_works.*","designations.label as designation_label","statuses.value as status_value","statuses.label as status_label")
        ->where("repair_works.repair_id",$repair->id)
        ->get();

        $repairer = Repairer::select("*")
                ->orderBy("repairers.created_at","DESC")
                ->where('repairers.id',$repair->repairer_id)
                ->first();

        $client = Client::select("*")
                ->orderBy("clients.created_at","DESC")
                ->where('clients.id',$repair->client_id)
                ->first();

        $vehicle = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                ->join('colors','colors.id','=','vehicles.color_id')
                ->join('statuses','statuses.id','=','vehicles.status_id')
                ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","statuses.value as status_value","statuses.label as status_label")
                ->orderByDesc("vehicles.created_at")
                ->where('vehicles.id',$repair->vehicle_id)
                ->first();

        $shock_point = ShockPoint::join('users','users.id','=','shock_points.created_by')
                ->join('statuses','statuses.id','=','shock_points.status_id')
                ->select("shock_points.*","statuses.value as status_value","statuses.label as status_label")
                ->orderByDesc("shock_points.created_at")
                ->where('shock_points.id',$repair->shock_point_id)
                ->first();

        $path = base_path('public/images/logo_eg.jpg');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $path_check_icon = base_path('public/images/check-icon.png');
        $type_check_icon = pathinfo($path_check_icon, PATHINFO_EXTENSION);
        $data_check_icon = file_get_contents($path_check_icon);
        $check_icon = 'data:image/'.$type_check_icon.';base64,'.base64_encode($data_check_icon);

        #$path_signature = public_path('storage/signature/'.$user_signature);
        #$type_signature = pathinfo($path_signature, PATHINFO_EXTENSION);
        #$data_signature = file_get_contents($path_signature);
        #$signature = 'data:image/'.$type_signature.';base64,'.base64_encode($data_signature);

        $path_wbg = base_path('public/images/wbg.png');
        $type_wbg = pathinfo($path_wbg, PATHINFO_EXTENSION);
        $data_wbg = file_get_contents($path_wbg);
        $wbg = 'data:image/'.$type_wbg.';base64,'.base64_encode($data_wbg);

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        // $pdf = PDF::loadView('repair/index',compact('repair','repair_works','user','repairer','client','vehicle','shock_point','now','numberTransformer','logo','check_icon','signature','wbg'));
        // $pdf->set_option('isHtml5ParserEnabled', true);
        // $pdf->set_option('isRemoteEnabled', true);
        // $pdf->setOptions(['defaultFont' => 'sans-serif']);
        // $pdf->save(public_path("storage/repair/".$repair->reference.".pdf"));
        // $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

        $file = public_path("storage/repair/".$repair->reference.".pdf");

        $email = 'brahimafane06@gmail.com';

        if($repair){

            $file_exist = public_path("storage/repair/".$repair->reference.".pdf");

            if($file_exist){
                $emails = $request->get('emails');
                $nb_email = count($emails);

                if($nb_email > 0){
                    Mail::to($emails)->cc('brahimafane06@gmail.com')->send(new SendMail($file_exist,$repair->reference));
                }

                if (Mail::failures()) {
                    $resp = "NOKNOH";
                } else {
                    $resp = "OKOKOK";
                }

            } else {
                $pdf = PDF::loadView('repair/index',compact('repair','qr_code','repair_works','user','repairer','vehicle','shock_point','now','numberTransformer','logo','check_icon','wbg'));
                $pdf->set_option('isHtml5ParserEnabled', true);
                $pdf->set_option('isRemoteEnabled', true);
                $pdf->setOptions(['defaultFont' => 'sans-serif']);
                $pdf->save(public_path("storage/repair/".$repair->reference.".pdf"));
                $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

                $file = public_path("storage/repair/".$repair->reference.".pdf");

                $emails = $request->get('emails');
                $nb_email = count($emails);

                if($file &&  $nb_email > 0){
                    Mail::to($emails)->cc('brahimafane06@gmail.com')->send(new SendMail($file,$repair->reference));
                }

            }

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Réparation renvoyée avec succès.',
                    'repair' => $repair,
                    'resp' => $resp,
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

    public function download_repair($reference)
    {
        $path = public_path("storage/repair/".$reference.".pdf");

        if (file_exists($path)) {
            return response()->download($path);
        }

        return response()->json(['message' => 'File not found.'], 404);
    }

    public function download_photo($reference)
    {
        $path = public_path("storage/before_photos/".$reference.".png");

        if (!file_exists($path)) {
            $path = public_path("storage/during_photos/".$reference.".png");
        }

        if (!file_exists($path)) {
            $path = public_path("storage/after_photos/".$reference.".png");
        }

        if (file_exists($path)) {
            return response()->download($path);
        }

        return response()->json(['message' => 'File not found.'], 404);
    }


    public function add_before_photos(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $repair = Repair::where('repairs.id', $request->id)->first();

        if($repair){

            $files = [];
            if($request->hasfile('before_photos'))
            {
                $count = 0;
                foreach($request->file('before_photos') as $file)
                {
                    $count = $count + 1;
                    $file_name = 'IMG_BP'.$today.'_'.$count;
                    // $name = 'IMG_BP'.$today.'_'.$count.'.'.$file->getClientOriginalExtension();
                    $name = 'IMG_BP'.$today.'_'.$count.'.png';
                    $file->move(public_path('storage/before_photos'), $name);
                    $files[] = $file_name;
                }
            }

            $repair->update(
                [
                    'before_photos' => $files,
                    'before_photos_added_by' => $userId,
                    'before_photos_added_at' => $now,
                    'status_id' => 4
                ]
            );

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Photos ajoutées avec succès.',
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

    public function add_during_photos(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $repair = Repair::where('repairs.id', $request->id)->first();

        if($repair){

            $files = [];
            if($request->hasfile('during_photos'))
            {
                $count = 0;
                foreach($request->file('during_photos') as $file)
                {
                    $count = $count + 1;
                    $file_name = 'IMG_DP'.$today.'_'.$count;
                    // $name = 'IMG_DP'.$today.'_'.$count.'.'.$file->getClientOriginalExtension();
                    $name = 'IMG_DP'.$today.'_'.$count.'.png';
                    $file->move(public_path('storage/during_photos'), $name);
                    $files[] = $file_name;
                }
            }

            $repair->update(
                [
                    'during_photos' => json_encode($files),
                    'during_photos_added_by' => $userId,
                    'during_photos_added_at' => $now,
                    'status_id' => 5
                ]
            );

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Photos ajoutées avec succès.',
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

    public function add_after_photos(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $repair = Repair::where('repairs.id', $request->id)->first();

        if($repair){

            $files = [];
            if($request->hasfile('after_photos'))
            {
                $count = 0;
                foreach($request->file('after_photos') as $file)
                {
                    $count = $count + 1;
                    $file_name = 'IMG_AP'.$today.'_'.$count;
                    // $name = 'IMG_AP'.$today.'_'.$count.'.'.$file->getClientOriginalExtension();
                    $name = 'IMG_AP'.$today.'_'.$count.'.png';
                    $file->move(public_path('storage/after_photos'), $name);
                    $files[] = $file_name;
                }
            }

            $repair->update(
                [
                    'after_photos' => json_encode($files),
                    'after_photos_added_by' => $userId,
                    'after_photos_added_at' => $now,
                    'status_id' => 6
                ]
            );

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Photos ajoutées avec succès.',
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

    public function generate_file(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $user_signature = $user->signature;

        $now = Carbon::now();

        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;
        $reference = 'FTS'.$today;

        $path = base_path('public/images/logo_eg.jpg');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $path_check_icon = base_path('public/images/check-icon.png');
        $type_check_icon = pathinfo($path_check_icon, PATHINFO_EXTENSION);
        $data_check_icon = file_get_contents($path_check_icon);
        $check_icon = 'data:image/'.$type_check_icon.';base64,'.base64_encode($data_check_icon);

        $path_wbg = base_path('public/images/wbg.png');
        $type_wbg = pathinfo($path_wbg, PATHINFO_EXTENSION);
        $data_wbg = file_get_contents($path_wbg);
        $wbg = 'data:image/'.$type_wbg.';base64,'.base64_encode($data_wbg);

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        $repairs = Repair::join('vehicles','vehicles.id','=','repairs.vehicle_id')
                ->join('repairers','repairers.id','=','repairs.repairer_id')
                ->join('clients','clients.id','=','repairs.client_id')
                ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                ->join('users','users.id','=','repairs.created_by')
                ->join('statuses','statuses.id','=','repairs.status_id')
                ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                        "vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                        "clients.name as client_name","clients.address as client_address","clients.phone as client_phone","clients.email as client_email","shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                ->first();

        $nb = 0;

        foreach($repairs as $repair){
            $repair_works = RepairWork::join('repairs','repairs.id','=','repair_works.repair_id')
                ->join('designations','designations.id','=','repair_works.designation_id')
                ->join('statuses','statuses.id','=','repair_works.status_id')
                ->select("repair_works.*","designations.label as designation_label","statuses.value as status_value","statuses.label as status_label")
                ->where("repair_works.repair_id",$repair->id)
                ->get();

            $repairer = Repairer::select("*")
                    ->orderBy("repairers.created_at","DESC")
                    ->where('repairers.id',$repair->repairer_id)
                    ->first();

            $client = Client::select("*")
                    ->orderBy("clients.created_at","DESC")
                    ->where('clients.id',$repair->client_id)
                    ->first();

            $vehicle = Vehicle::join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('statuses','statuses.id','=','vehicles.status_id')
                    ->select("vehicles.*","brands.label as brand_label","colors.label as color_label","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("vehicles.created_at")
                    ->where('vehicles.id',$repair->vehicle_id)
                    ->first();

            $shock_point = ShockPoint::join('users','users.id','=','shock_points.created_by')
                    ->join('statuses','statuses.id','=','shock_points.status_id')
                    ->select("shock_points.*","statuses.value as status_value","statuses.label as status_label")
                    ->orderByDesc("shock_points.created_at")
                    ->where('shock_points.id',$repair->shock_point_id)
                    ->first();

            #$path_signature = public_path('storage/signature/'.$user_signature);
            #$type_signature = pathinfo($path_signature, PATHINFO_EXTENSION);
            #$data_signature = file_get_contents($path_signature);
            #$signature = 'data:image/'.$type_signature.';base64,'.base64_encode($data_signature);

            $pdf = PDF::loadView('repair/index',compact('repair','repair_works','user','repairer','client','vehicle','shock_point','now','numberTransformer','logo','check_icon','wbg'));
            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->setOptions(['defaultFont' => 'sans-serif']);
            $pdf->save(public_path("storage/repair/".$repair->reference.".pdf"));
            $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

            $file = public_path("storage/repair/".$repair->reference.".pdf");

            $nb++;
        }

        if($file){
            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Fichier généré avec succès.',
                    'traite' => $nb.'/'.count($repairs),
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
        $repair = Repair::with('repairer','vehicle','insurer','client','user','shocks','status')
                ->where('repairs.id', $id)
                ->orderByDesc("repairs.created_at")
                ->first();

        $qr_code = QrCode::where('status_id', 1)->first();

        

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparations.',
                'repair' => $repair,
                'qr_code' => $qr_code,
                'emails' => json_decode($repair->emails),
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
        $mois_jour_heure = date("mdH");
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

    public function search($information)
    {
        $user = Auth::user();

        if($user->profil_id == 1 || $user->profil_id == 2){
            $repairs = Repair::join('vehicles','vehicles.id','=','repairs.vehicle_id')
                    ->join('repairers','repairers.id','=','repairs.repairer_id')
                    ->join('clients','clients.id','=','repairs.client_id')
                    ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                    ->join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('users','users.id','=','repairs.created_by')
                    ->join('statuses','statuses.id','=','repairs.status_id')
                    ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                            "vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                            "clients.name as client_name","clients.address as client_address","clients.phone as client_phone","clients.email as client_email","brands.label as brand_label","colors.label as color_label","shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                    ->where('repairs.reference','like', '%'.$information.'%')
                    ->orWhere('vehicles.license_plate','like', '%'.$information.'%')
                    ->orderByDesc("repairs.created_at")
                    ->paginate(10);
        } else {
            $repairs = Repair::join('vehicles','vehicles.id','=','repairs.vehicle_id')
                    ->join('repairers','repairers.id','=','repairs.repairer_id')
                    ->join('clients','clients.id','=','repairs.client_id')
                    ->join('shock_points','shock_points.id','=','repairs.shock_point_id')
                    ->join('brands','brands.id','=','vehicles.brand_id')
                    ->join('colors','colors.id','=','vehicles.color_id')
                    ->join('users','users.id','=','repairs.created_by')
                    ->join('statuses','statuses.id','=','repairs.status_id')
                    ->select("repairs.*","vehicles.license_plate as vehicle_license_plate","vehicles.model as vehicle_model","vehicles.type as vehicle_type","vehicles.option as vehicle_option",
                            "vehicles.mileage as vehicle_mileage","repairers.name as repairer_name","repairers.address as repairer_address","repairers.phone as repairer_phone","repairers.email as repairer_email","repairers.responsible_first_name as repairer_responsible_first_name","repairers.responsible_last_name as repairer_responsible_last_name",
                            "clients.name as client_name","clients.address as client_address","clients.phone as client_phone","clients.email as client_email","brands.label as brand_label","colors.label as color_label","shock_points.label as shock_point_label","users.first_name as user_first_name","users.last_name as user_last_name","users.phone as user_phone","users.email as user_email","users.signature as user_signature","statuses.value as status_value","statuses.label as status_label")
                    ->where("repairers.id", $user->repairer_id)
                    // ->where('repairs.reference','like', '%'.$information.'%')
                    // ->orWhere('vehicles.license_plate','like', '%'.$information.'%')
                    ->where('vehicles.license_plate','like', '%'.$information.'%')
                    ->orderByDesc("repairs.created_at")
                    ->paginate(10);
        }

        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Liste des réparations.',
                'repairs' => $repairs
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
