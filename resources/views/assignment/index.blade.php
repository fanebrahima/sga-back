<!DOCTYPE html>
<html lang="str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Avis d'écheance Santé / Sanlam-CI</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  
        <link href="asset('css/app.css') }}" rel="stylesheet">
        <script src="asset('js/app.js') }}" defer></script>

        

        <style>
            table, caption, th, td {

            border: 1px solid;
            border-collapse: collapse;
            align: center;

            }
            caption, th, td {

            padding: 8px;

            }

            table, caption, th, td {

            border: 1px solid;
            border-collapse: collapse;

            }
            caption, th, td {

            padding: 8px;

            }

            th {

            background-color: silver;

            }
            .left-panel
            {        
                width:40%;
                height:100px;
                float:left;  
                text-align:center;  
                padding-left:50px;        
            }
            .right-panel
            {        
                width:40%;
                height:100px;
                float:right;
                text-align:center;
                padding-left:50px;
            }
            .span_left_1
            {
                float:left; 
                width:100%;
            }

            .span_left
            {
                float:left; 
                width:40%;
            }

            .span_right
            {
                float:right; 
                width:60%;
            }

            .clearfix::after {
                content: "";
                clear: both;
                display: table;
            }

            .left-panel
            {        
                width:40%;
                height:100px;
                float:left;   
                text-align:center;
            }
            .right-panel
            {        
                width:40%;
                height:100px;
                float:right;
                text-align:center;
            }

        </style>
    </head>
    <body class="antialiased">
        <div class="container">
            <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0 mt-5">
                <div class="clearfix">
                    <div style="float:left; width:49%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left_1">CABINET GERENTHON ET CIE </span><br>
                        
                            <span class="span_left_1">EXPERTISES AUTOMOBILES</span><br>
                        
                            <span class="span_left_1">Abidjan (Côte d'Ivoire)</span><br>
                        
                            <span class="span_left_1">Rue Lumière Edition</span><br>
                        
                            <span class="span_left_1">01 BP 2173 Abidjan 01</span><br>
                        
                            <span class="span_left_1">Tel : 27 21 35 17 12 <br> <span style="padding-left: 32px;">27 21 35 91 32</span> <br> <span style="padding-left: 32px;">27 21 35 92 41</span></span>
                        </p>
                        
                    </div>
                    <div style="float:right; width:30%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left"><img src="<?php echo $logo ?>" width="200px" height="50px"></span><br>
                        </p>
                        
                    </div> 
                </div>

                <br><br>

                <table style="width: 100%; text-align:center; margin-bottom:10px;">
                    <thead style="background-color: rgb(197, 213, 254);">
                        <tr>
                            <td>FICHIER D'EXPERTISE N° {{ $assignment_exist->number }}</td>
                        </tr>
                    </thead>
                    
                </table>

                <br>
                <br>

                <table style="width: 100%; text-align:center; margin-bottom:10px;">
                    <thead>
                        <tr>
                            <td>INFORMATIONS DU SINISTRE</td>
                        </tr>
                    </thead>
                    
                </table>

                <div class="clearfix">
                    <div style="float:left; width:49%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left">Nom du client : </span><span class="span_right">{{ $assignment_exist->client_first_name }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Prénoms du client : </span><span class="span_right">{{ $assignment_exist->client_last_name }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Contact du client  : </span><span class="span_right">{{ $assignment_exist->client_phone }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Numéro de police : </span><span class="span_right">{{ $assignment_exist->policy_number }}</span><br>
                        </p>
                        
                    </div>
                    <div style="float:right; width:49%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left">Numéro de sinistre : </span><span class="span_right">{{ $assignment_exist->disaster_number }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Date du sinistre : </span><span class="span_right">{{ $assignment_exist->disaster_date }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Date d'expertise : </span><span class="span_right">{{ $assignment_exist->expertise_date }}</span><br>
                        </p>
                    </div> 
                </div>

                <br><br>

                <table style="width: 100%; text-align:center; margin-bottom:10px;">
                    <thead>
                        <tr>
                            <td>CARACTERISTIQUE DU VEHICULE </td>
                        </tr>
                    </thead>
                    
                </table>

                <div class="clearfix">
                    <div style="float:left; width:49%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left">Immatriculation : </span><span class="span_right">{{ $assignment_exist->car_immatriculation }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Marque : </span><span class="span_right">{{ $assignment_exist->car_brand }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Modèle  : </span><span class="span_right">{{ $assignment_exist->car_model }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Couleur : </span><span class="span_right">{{ $assignment_exist->car_color }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Nombre de place : </span><span class="span_right">{{ $assignment_exist->car_nb_place }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Puissance fiscale : </span><span class="span_right">{{ $assignment_exist->car_fiscal_powerful }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Energie : </span><span class="span_right">{{ $assignment_exist->car_energy }}</span><br>
                        </p>
                        
                    </div>
                    <div style="float:right; width:49%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left">Numéro de série : </span><span class="span_right">{{ $assignment_exist->car_serial_number }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Genre : </span><span class="span_right">{{ $assignment_exist->car_gender }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">KM Compteur : </span><span class="span_right">{{ $assignment_exist->car_km_comptor }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Etat général : </span><span class="span_right">{{ $assignment_exist->car_general_state }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Valeur neuve : </span><span class="span_right">{{ $assignment_exist->car_new_value }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Depreciation : </span><span class="span_right">{{ $assignment_exist->car_depreciation }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Valeur vénale : </span><span class="span_right">{{ $assignment_exist->car_market_value }}</span><br>
                        </p>
                        <p>
                            <span class="span_left">Première mise en circulation : </span><span class="span_right">{{ \Carbon\Carbon::parse($assignment_exist->car_first_circulation_date)->format('d/m/Y') }}</span><br>
                        </p>
                    </div> 
                </div>
            
            </div>
            <br>
            
            
        </div>
    </body>
</html>
