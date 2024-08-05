<!DOCTYPE html>
<html lang="str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Fiche des travaux {{$repair->reference}} / GERENTHON & CIE</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <link href="asset('css/app.css') }}" rel="stylesheet">
        <script src="asset('js/app.js') }}" defer></script>



        <style>



            <?php include(public_path().'/bootstrap/css/bootstrap.css');?>

            table, caption, th, td {
                border: 0px solid;
                font-size: 12px;
                padding: 2px;
            }

            body{
                font-family: "Times New Roman", Times, serif;
                font-size: 12px;
                margin-top: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 3cm;
            }

            .watermark {
                position: absolute;
                opacity: 0.12;
                font-size: 75px;
                width: 100%;
                z-index: 100;
                transform: rotate(-45deg);
                text-align: center;
            }

            @page {
                margin: 0cm 0cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;

                /** Extra personal styles **/
                text-align: center;
                line-height: 1.5cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;
                margin-left: 1cm;
                margin-right: 1cm;
                font-size: 9px;

                /** Extra personal styles **/
                text-align: center;
            }

        </style>
    </head>
    <body class="antialiased">
        {{-- <header>
            Our Code World
        </header> --}}
        <footer>
            <hr style="border: 1px solid black;">
            SARLU au capital de 7 000 000 FCFA - Cocody, Rivera Golf, Immeuble FRAKE 1er Etage porte 812 - RCCM CI-ABJ-03-2022-B13-11908-NCC 2245465 N 04 BP 2032 ABIDJAN 04<br>
            N°Compte BNI : CI092 01002 0022199600003 55 TEL : +225 27 22 28 82 75 - CEL : +225 07 07 36 35 45  EMAIL : sgaexpertise@gmail.com - www.sgaexpertise.ci
        </footer>

        <table class="table text-center">
            <thead style="border: 1px solid; font-size: 12px;">
            <tr style="border: 1px solid; font-size: 12px;">
                <th style="border: 1px solid; font-size: 12px; vertical-align: middle;">
                    <img src="{{$logo}}" alt="logo" style="text-align: center; width:100px; height:40px;">
                </th>
                <th style="border: 1px solid; font-size: 12px; vertical-align: middle;">FICHE D'EXPERTISE {{$repair->reference}}</th>
                <th style="border: 1px solid; font-size: 12px; vertical-align: middle;">
                    DATE: {{ \Carbon\Carbon::parse($repair->created_at)->format('d/m/Y') }}
                    <br>
                    <img src="{{$qr_code->qr_code}}" alt="qr_code" style="text-align: center; width:100px; height:100px;">
                </th>
            </tr>
            </thead>
        </table>

        <table class="table text-center">
            <thead style="border: 1px solid; font-size: 12px;">
                <tr style="border: 1px solid; font-size: 12px;">
                <th colspan="3" style="border: 1px solid; font-size: 12px;">DOSSIER SUIVI PAR :</th>
                </tr>
            </thead>
            <thead style="border: 1px solid; font-size: 12px;">
                <tr style="border: 1px solid; font-size: 12px;">
                    <th style="border: 1px solid; font-size: 12px;">EXPERT</th>
                    <th style="border: 1px solid; font-size: 12px;">REPARATEUR</th>
                    <th style="border: 1px solid; font-size: 12px;">CLIENT</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border: 1px solid; font-size: 12px;">
                    <td style="border: 1px solid; font-size: 12px;">
                        <div class="text-left d-flex flex-column bd-highlight" style="text-align:left;">
                            <div class="p-1 bd-highlight">NOM : <b>{{$repair->user->last_name}} {{$repair->user->first_name}}</b></div>
                            <div class="p-1 bd-highlight">CONTACT : <b>{{$repair->user->phone}}</b></div>
                            <div class="p-1 bd-highlight">E-MAIL : <b>{{$repair->user->email}}</b></div>
                        </div>
                    </td>
                    <td style="border: 1px solid; font-size: 12px;">
                        <div class="text-left d-flex flex-column bd-highlight" style="text-align:left;">
                            <div class="p-1 bd-highlight">NOM : <b>{{$repair->repairer->name}}</b></div>
                            <div class="p-1 bd-highlight">CONTACT : <b>{{$repair->repairer->phone}}</b></div>
                            <div class="p-1 bd-highlight">E-MAIL : <b>{{$repair->repairer->email}}</b></div>
                        </div>
                    </td>
                    <td style="border: 1px solid; font-size: 12px;">
                        <div class="text-left d-flex flex-column bd-highlight" style="text-align:left;">
                            <div class="p-1 bd-highlight">NOM : <b>{{$repair->client->name}}</b></div>
                            <div class="p-1 bd-highlight">CONTACT : <b>{{$repair->client->phone}}</b></div>
                            <div class="p-1 bd-highlight">E-MAIL : <b>{{$repair->client->email}}</b></div>
                            <div class="p-1 bd-highlight">Assureur : <b>{{$repair->insurer->name}}</b></div>
                            <div class="p-1 bd-highlight">Numéro de sinistre : <b>{{$repair->disaster_number}}</b></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>


        <table class="table table-bordered text-center">
            <thead style="border: 1px solid; font-size: 12px;">
                <tr style="border: 1px solid; font-size: 12px;">
                    <th colspan="5" style="border: 1px solid; font-size: 12px;">IDENTIFICATION DU VEHICULE</th>
                </tr>
            </thead>
            <thead style="border: 1px solid; font-size: 12px;">
                <tr style="border: 1px solid; font-size: 12px;">
                    <th style="border: 1px solid; font-size: 12px;">IMMATRICULATION</th>
                    <th style="border: 1px solid; font-size: 12px;">MARQUE</th>
                    <!-- <th style="border: 1px solid; font-size: 12px;">MODELE - TYPE - OPTION</th> -->
                    @if(!$repair->vehicle->type && !$repair->vehicle->option)
                    <th style="border: 1px solid; font-size: 12px;">MODELE</th>
                    @elseif(!$repair->vehicle->type)
                    <th style="border: 1px solid; font-size: 12px;">MODELE - OPTION</th>
                    @elseif(!$repair->vehicle->option)
                    <th style="border: 1px solid; font-size: 12px;">MODELE - TYPE</th>
                    @else
                    <th style="border: 1px solid; font-size: 12px;">MODELE - TYPE - OPTION</th>
                    @endif
                    <th style="border: 1px solid; font-size: 12px;">COULEUR</th>
                    <th style="border: 1px solid; font-size: 12px;">KILOMETRAGE</th>
                </tr>
                </thead>
            <tbody>
                <tr style="border: 1px solid; font-size: 12px;">
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->license_plate}}</td>
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->brand_label}}</td>
                    <!-- <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->model}} - {{$repair->vehicle->type}} - {{$repair->vehicle->option}}</td> -->
                    @if(!$repair->vehicle->type && !$repair->vehicle->option)
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->model}}</td>
                    @elseif(!$repair->vehicle->type && $repair->vehicle->option)
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->model}} - {{$repair->vehicle->option}}</td>
                    @elseif($repair->vehicle->type && !$repair->vehicle->option)
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->model}} - {{$repair->vehicle->type}}</td>
                    @else
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->model}} - {{$repair->vehicle->type}} - {{$repair->vehicle->option}}</td>
                    @endif
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->color_label}}</td>
                    <td style="border: 1px solid; font-size: 12px;">{{$repair->vehicle->mileage}} km</td>
                </tr>
            </tbody>
        </table>

        <div class="watermark">{{$repair->reference}} {{$repair->vehicle->license_plate}}</div>

        @foreach($repair->shocks as $shock)
            <div class="text-left d-flex flex-column bd-highlight mt-2 mb-1">
                <div class="p-2 bd-highlight"><b>POINT DE CHOC</b> : {{$shock->shock_point->label}}</div>
            </div>

            <?php  $i = 0; ?>

            <table class="table table-bordered text-center">
                <thead style="border: 1px solid; font-size: 12px; background-color: rgb(223, 221, 218);">
                    <tr style="border: 1px solid; font-size: 12px;">
                        <th colspan="6" style="border: 1px solid; font-size: 12px;">TRAVAUX A FAIRE SOUS RESERVE DE DEMONTAGE</th>
                    </tr>
                </thead>
                <thead style="border: 1px solid;
                    font-size: 12px;
                   ">
                    <tr style="border: 1px solid; font-size: 12px;">
                        <th style="border: 1px solid; font-size: 12px;">N°</th>
                        <th style="border: 1px solid; font-size: 12px;">DESIGNATION</th>
                        <th style="border: 1px solid; font-size: 12px;">REMPLACEMENT</th>
                        <th style="border: 1px solid; font-size: 12px;">REPARATION</th>
                        <th style="border: 1px solid; font-size: 12px;">PEINDRE</th>
                        <th style="border: 1px solid; font-size: 12px;">CONTRÔLE</th>
                    </tr>
                    </thead>
                <tbody>
                    @foreach($shock->repair_works as $item)
                    <?php  $i = $i + 1; ?>
                    <tr style="border: 1px solid; font-size: 12px;">
                        <td style="border: 1px solid; font-size: 12px;">{{ $i }}</td>
                        <td style="border: 1px solid; font-size: 12px;">{{$item->designation->label}}</td>
                        <td style="border: 1px solid; font-size: 12px;">
                            @if($item->replacement)
                            <img src="{{$check_icon}}" alt="" width="15" style="padding-top:2px;">
                            @endif
                        </td>
                        <td style="border: 1px solid; font-size: 12px;">
                            @if($item->repair)
                            <img src="{{$check_icon}}" alt="" width="15" style="padding-top:2px;">
                            @endif
                        </td>
                        <td style="border: 1px solid; font-size: 12px;">
                            @if($item->paint)
                            <img src="{{$check_icon}}" alt="" width="15" style="padding-top:2px;">
                            @endif
                        </td>
                        <td style="border: 1px solid; font-size: 12px;">
                            @if($item->control)
                            <img src="{{$check_icon}}" alt="" width="15" style="padding-top:2px;">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <div class="text-left d-flex flex-column bd-highlight" style="background-color: rgb(223, 221, 218);">
            <div class="p-2 bd-highlight"><b>NOTE D'EXPERT</b> :
                {{$repair->remark}}
            </div>
        </div>
        <table class="table text-center table-borderless">
            <thead style="border: 0px solid; font-size: 12px; padding: 5px;">
                <tr style="border: 0px solid; font-size: 12px; padding: 5px;">
                    <th style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        EXPERT
                    </th>
                    <th style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        CLIENT
                    </th>
                    <th style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        REPARATEUR
                    </th>
                </tr>
                <tr style="border: 0px solid; font-size: 12px; padding: 5px;">
                    @if($repair->expert_signature)
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$repair->expert_signature}}" width="190" height="100" alt="expert_signature">
                    </td>
                    @else
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$wbg}}" width="190" height="100" alt="expert_signature">
                    </td>
                    @endif
                    @if($repair->customer_signature)
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$repair->customer_signature}}" width="190" height="100" alt="customer_signature">
                    </td>
                    @else
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$wbg}}" width="190" height="100" alt="customer_signature">
                    </td>
                    @endif
                    @if($repair->repairer_signature)
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$repair->repairer_signature}}" width="190" height="100" alt="repairer_signature">
                    </td>
                    @else
                    <td style="border: 0px solid; font-size: 12px; padding: 5px;" class="p-3">
                        <img src="{{$wbg}}" width="190" height="100" alt="repairer_signature">
                    </td>
                    @endif
                </tr>
            </thead>
        </table>
    </body>
</html>
