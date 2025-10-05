<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyAdvanceExportController extends Controller
{
    public function exportSage(Request $request): StreamedResponse
    {
        $company = auth('web')->user();

        // Filtre période (optionnel)
        $start = $request->get('start');
        $end   = $request->get('end');

        $q = SalaryAdvance::with(['employee','payout'])
            ->where('company_id',$company->id);

        if ($start) $q->whereDate('created_at','>=',$start);
        if ($end)   $q->whereDate('created_at','<=',$end);

        $advances = $q->get();

        $filename = "export_sage_{$company->code}_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'code_entreprise','matricule','nom','prenom',
            'montant','date','type','compte','reference','narration'
        ];

        $callback = function() use ($advances,$company,$columns) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM pour Excel/Sage
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-tête
            fputcsv($out, $columns, ';');

            foreach ($advances as $a) {
                fputcsv($out, [
                    $company->code,
                    $a->employee?->matricule,
                    $a->employee?->last_name,
                    $a->employee?->first_name,
                    $a->amount_final,
                    $a->created_at->format('Y-m-d'),
                    'Avance sur salaire',
                    '421',
                    'ADV-'.$a->id,
                    'Avance via PengMe'
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback,200,$headers);
    }
}
