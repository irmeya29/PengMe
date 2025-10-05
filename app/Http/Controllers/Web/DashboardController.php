<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Employee, SalaryAdvance};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company   = auth('web')->user(); // Entreprise connectée
        $companyId = $company->id;

        // Période (défaut = mois en cours)
        $start = $request->get('start');
        $end   = $request->get('end');
        if (!$start && !$end) {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end   = Carbon::now()->endOfMonth()->toDateString();
            $request->merge(['start'=>$start,'end'=>$end]);
        }

        // Base filtrée
        $base = SalaryAdvance::with(['payout','employee'])
            ->where('company_id', $companyId)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // KPIs
        $employeesCount = Employee::where('company_id',$companyId)->count();
        $advancesCount  = (clone $base)->count();
        $totalAmount    = (clone $base)->sum('amount_final');

        $approvedCount  = (clone $base)->where('status','approved')->count();
        $rejectedCount  = (clone $base)->where('status','rejected')->count();

        $payoutSuccess  = (clone $base)->whereHas('payout', fn($q)=>$q->where('status','success'))->count();
        $payoutPending  = (clone $base)->whereHas('payout', fn($q)=>$q->where('status','pending'))->count();
        $payoutFailed   = (clone $base)->whereHas('payout', fn($q)=>$q->where('status','failed'))->count();

        $successRate    = $advancesCount > 0 ? round(($payoutSuccess / $advancesCount) * 100, 1) : 0;

        // Total à rembourser à l’administrateur (virements succès uniquement)
        $adminDue = (clone $base)
            ->whereHas('payout', fn($q)=>$q->where('status','success'))
            ->sum('amount_final');

        $adminDueCount = (clone $base)
            ->whereHas('payout', fn($q)=>$q->where('status','success'))
            ->count();

        // Top 5 employés & dernières demandes
        $topEmployees = (clone $base)
            ->select('employee_id', DB::raw('SUM(amount_final) as total'))
            ->groupBy('employee_id')
            ->with('employee')
            ->orderByDesc('total')->limit(5)->get();

        $recent = (clone $base)->latest('id')->limit(8)->get();

        return view('company.dashboard', compact(
            'company',
            'employeesCount','advancesCount','totalAmount',
            'approvedCount','rejectedCount',
            'payoutSuccess','payoutPending','payoutFailed','successRate',
            'adminDue','adminDueCount',
            'topEmployees','recent','start','end'
        ));
    }
}
