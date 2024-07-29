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
            .span_left
            {
                float:left; 
                width:100%;
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
                            <span class="span_left">CABINET GERENTHON ET CIE </span><br>
                        
                            <span class="span_left">EXPERTISES AUTOMOBILES</span><br>
                        
                            <span class="span_left">Abidjan (Côte d'Ivoire)</span><br>
                        
                            <span class="span_left">Rue Lumière Edition</span><br>
                        
                            <span class="span_left">01 BP 2173 Abidjan 01</span><br>
                        
                            <span class="span_left">Tel : 27 21 35 17 12 <br> <span style="padding-left: 32px;">27 21 35 91 32</span> <br> <span style="padding-left: 32px;">27 21 35 92 41</span></span>
                        </p>
                        
                    </div>
                    <div style="float:right; width:30%; padding-left: 4px; padding-bottom: 15px;">
                        <p>
                            <span class="span_left"><img src="<?php echo $logo ?>" width="200px" height="50px"></span><br>
                        </p>
                        
                    </div> 
                </div>

                <p><h3 style="text-align:center;">RECU N° {{$voucher_history->number}}</h3></p>

                <div style="padding-left: 4px; padding-bottom: 15px;">
                    <p>
                        <span>EXPERT : GERENTHON ET CIE</span><br>
                    
                        <span>RAPPORT N° {{$voucher_history->report_number}}</span><br>
                    
                        <span>Reçu de {{$voucher_history->label}}</span><br>
                    
                        <span>La somme (en chiffre) de {{number_format($voucher_history->amount_paid, 0, ',', ' ')}} FCFA.</span><br>

                        <span>La somme (en lettre) de {{$numberTransformer->toWords($voucher_history->amount_paid)}} Francs CFA.</span><br>
                    
                        <span>Pour sur <span style="text-transform: lowercase;">{{$voucher_history->voucher_type_label}}</span> honoraires du rapport d'expertise ci-dessus référencé.</span><br>
                    
                    </p>
                    
                </div>

                <div style="width:100%;">

                    <p style="float:left; padding-left: 60px; font-size:15px;"><b>Signature</b></p>

                    <p style="float:right; padding-right: 60px; font-size:15px;">Abidjan, le {{ \Carbon\Carbon::parse($now)->format('d/m/Y') }}</p>

                </div>

                
                

                
            
            </div>
            <br>
            
            
        </div>
    </body>
</html>
