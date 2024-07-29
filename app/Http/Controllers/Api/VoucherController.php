<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use NumberToWords\NumberToWords;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = Voucher::join('voucher_types','voucher_types.id','=','vouchers.voucher_type_id')
                    ->join('statuses','statuses.id','=','vouchers.status_id')
                    ->select("vouchers.*","statuses.value as status_value","statuses.label as status_label","voucher_types.value as voucher_type_value","voucher_types.label as voucher_type_label")
                    ->where('vouchers.etat',1)
                    ->orderByDesc("vouchers.updated_at")
                    ->get();

        //$user = User::find($userId);
        //$token = $user->createToken('myapptoken')->plainTextToken;
        //$a = $user->name;
        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Liste des missions.', 
                'vouchers' => $vouchers
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
        $voucher_number = $today;

        $now = Carbon::now();


        $this->validate($request, [
            'report_number' => 'required',
            'label' => 'required',
            'amount' => 'required',
            'amount_rest' => 'required',
            'amount_paid' => 'required',
            'payment_type_id' => 'required',
        ]);


        if($request->amount_rest == 0){
            $voucher_type_id = 1;
            $tatus_id = 6;
        } else {
            $voucher_type_id = 2;
            $tatus_id = 7;
        }

        $voucher = Voucher::create(
            [
                'number' => $voucher_number,
                'report_number' => $request->report_number,
                'label' => $request->label,
                'amount' => $request->amount,
                'amount_rest' => $request->amount_rest,
                'amount_paid' => $request->amount_paid,
                'voucher_type_id' => $voucher_type_id,
                'file' => $voucher_number.".pdf",
                'status_id' => $tatus_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $voucher_histories = VoucherHistory::create(
            [
                'voucher_id' => $voucher->id,
                'number' => $voucher->number,
                'report_number' => $voucher->report_number,
                'label' => $voucher->label,
                'amount' => $voucher->amount,
                'amount_rest' => $voucher->amount_rest,
                'amount_paid' => $voucher->amount_paid,
                'voucher_type_id' => $voucher->voucher_type_id,
                'payment_type_id' => $request->payment_type_id,
                'file' => $voucher_number.".pdf",
                'status_id' => $voucher->status_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $voucher_history = VoucherHistory::join('payment_types','payment_types.id','=','voucher_histories.payment_type_id')
            ->join('vouchers','vouchers.id','=','voucher_histories.voucher_id')
            ->join('voucher_types','voucher_types.id','=','voucher_histories.voucher_type_id')
            ->join('statuses','statuses.id','=','voucher_histories.status_id')
            ->select("voucher_histories.*","statuses.value as status_value","statuses.label as status_label","voucher_types.value as voucher_type_value","voucher_types.label as voucher_type_label","payment_types.value as payment_type_value","payment_types.label as payment_type_label")
            ->where('voucher_histories.id',$voucher_histories->id)
            ->first();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        $path = base_path('public/images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $pdf = PDF::loadView('voucher/index',compact('voucher_history','now','numberTransformer','logo'));
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path("voucher/".$voucher->number.".pdf"));
        $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);


        if($voucher){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Reçu enregistré avec succès.', 
                    'voucher' => $voucher
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce reçu ne peut être enregistré en ce moment, veuillez réessayer plus tard !', 
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
        $voucher = Voucher::join('voucher_types','voucher_types.id','=','vouchers.voucher_type_id')
            ->join('statuses','statuses.id','=','vouchers.status_id')
            ->select("vouchers.*","statuses.value as status_value","statuses.label as status_label","voucher_types.value as voucher_type_value","voucher_types.label as voucher_type_label")
            ->where('vouchers.uuid',$uuid)
            ->first();

        $voucher_histories = VoucherHistory::join('payment_types','payment_types.id','=','voucher_histories.payment_type_id')
            ->join('vouchers','vouchers.id','=','voucher_histories.voucher_id')
            ->join('voucher_types','voucher_types.id','=','voucher_histories.voucher_type_id')
            ->join('statuses','statuses.id','=','voucher_histories.status_id')
            ->select("voucher_histories.*","statuses.value as status_value","statuses.label as status_label","voucher_types.value as voucher_type_value","voucher_types.label as voucher_type_label","payment_types.value as payment_type_value","payment_types.label as payment_type_label")
            ->where('voucher_histories.voucher_id',$voucher->id)
            ->orderByDesc("voucher_histories.created_at")
            ->get();
        

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Détail du reçu '.$uuid.'.',
                'voucher' => $voucher,
                'voucher_histories' => $voucher_histories,
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

        $now = Carbon::now();
        

        $this->validate($request, [
            'id' => 'required',
            'report_number' => 'required',
            'label' => 'required',
            'amount' => 'required',
            'amount_rest' => 'required',
            'amount_paid' => 'required',
            'payment_type_id' => 'required',
        ]);


        $voucher_updated_1 = Voucher::where('id',$request->id)->first();

        $new_amount_paid = $request->amount_paid + $voucher_updated_1->amount_paid;
        $new_amount_rest = $request->amount - ($request->amount_paid + $voucher_updated_1->amount_paid);

        if($new_amount_rest == 0){
            $voucher_type_id = 1;
            $tatus_id = 6;
        } else {
            $voucher_type_id = 2;
            $tatus_id = 7;
        }

        $voucherU = Voucher::where('id',$request->id)->update(
            [
                'amount_paid' => $new_amount_paid,
                'amount_rest' => $new_amount_rest,
                'voucher_type_id' => $voucher_type_id,
                'file' => $request->number.".pdf",
                'status_id' => $tatus_id,
                'updated_by' => $userId,
            ]
        );

        $voucher = Voucher::where('id',$request->id)->first();

        $voucher_histories = VoucherHistory::create(
            [
                'voucher_id' => $voucher->id,
                'number' => $voucher->number,
                'report_number' => $voucher->report_number,
                'label' => $voucher->label,
                'amount' => $voucher->amount,
                'amount_paid' => $request->amount_paid,
                'amount_rest' => $request->amount - ($request->amount_paid + $voucher_updated_1->amount_paid),
                'voucher_type_id' => $voucher_type_id,
                'payment_type_id' => $request->payment_type_id,
                'file' => $voucher->number.".pdf",
                'status_id' => $voucher->status_id,
                'etat' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        $voucher_history = VoucherHistory::join('payment_types','payment_types.id','=','voucher_histories.payment_type_id')
            ->join('vouchers','vouchers.id','=','voucher_histories.voucher_id')
            ->join('voucher_types','voucher_types.id','=','voucher_histories.voucher_type_id')
            ->join('statuses','statuses.id','=','voucher_histories.status_id')
            ->select("voucher_histories.*","statuses.value as status_value","statuses.label as status_label","voucher_types.value as voucher_type_value","voucher_types.label as voucher_type_label","payment_types.value as payment_type_value","payment_types.label as payment_type_label")
            ->where('voucher_histories.id',$voucher_histories->id)
            ->first();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        $path = base_path('public/images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/'.$type.';base64,'.base64_encode($data);

        $pdf = PDF::loadView('voucher/index',compact('voucher_history','now','numberTransformer','logo'));
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save(public_path("voucher/".$voucher->number.".pdf"));
        $pdf->setBasePath($_SERVER['DOCUMENT_ROOT']);


        if($voucher){
    
            return new JsonResponse(
                [
                    'success' => true, 
                    'message' => 'Reçu enregistré avec succès.', 
                    'voucher' => $voucher
                ], 
                201
            );

        } else {

            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Ce reçu ne peut être enregistré en ce moment, veuillez réessayer plus tard !', 
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

        $voucher_exist = Voucher::where('id', $request->id)->first();

        $report = '';
        if($request->hasfile('report')){
            $report_path = $request->file('report');
            $report_name = $voucher_exist->number.'.'.$report_path->getClientOriginalExtension();
            $report_path->move(public_path('voucher/reports'), $report_name);
            $report = $report_name; 

            $voucher = Voucher::where('id', $request->id)->update(
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
