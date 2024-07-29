<?php

namespace App\Exports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;


class CertificatesExport implements FromCollection
{
    private $company_uuid;

    public function __construct($company_uuid=0) 
    {
        $this->company_uuid = $company_uuid;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $certificates = Certificate::join('companies','companies.id','=','certificates.company_id')
                ->join('statuses','statuses.id','=','certificates.status_id')
                ->select("certificates.numero_immatriculation as Immatriculation", "certificates.numero_police as Numero_Police", "certificates.nom_assure as Nom_Assure", "certificates.intermediary as Intermediaire","companies.name as Nom_Compagnie","certificates.date_effet as Date_Effet","certificates.date_echeance as Date_Echeance")
                ->where("companies.uuid",$this->company_uuid)
                ->where("statuses.value",3)
                ->orderByDesc("certificates.created_at")
                ->get();

        
        $result = array();
        foreach($certificates as $item){
            
            $Date_Effet = Carbon::parse($item->Date_Effet)->format('d/m/Y');
            $Date_Echeance = Carbon::parse($item->Date_Echeance)->format('d/m/Y');

            $result[] = array(
                'id'=>$item->id,
                'Immatriculation' => $item->Immatriculation,
                'Numero_Police' => $item->Numero_Police,
                'Nom_Assure' => $item->Nom_Assure,
                'Intermediaire' => $item->Intermediaire,
                'Nom_Compagnie' => $item->Nom_Compagnie,
                'Date_Effet' => $Date_Effet,
                'Date_Echeance' => $Date_Echeance
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
          '#',
          'Immatriculation',
          'Numero_Police',
          'Nom_Assure',
          'Intermediaire',
          'Nom_Compagnie',
          'Date_Effet',
          'Date_Echeance'
        ];
    }
}
