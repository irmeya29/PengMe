<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, Employee, SalaryAdvance};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start');
        $end   = $request->get('end');

        // Période par défaut = mois en cours
        if (!$start && !$end) {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end   = Carbon::now()->endOfMonth()->toDateString();
            $request->merge(['start'=>$start,'end'=>$end]);
        }

        // bornes inclusives (avec heures)
        $from = Carbon::parse($start)->startOfDay();
        $to   = Carbon::parse($end)->endOfDay();

        // Entreprises
        $companiesActive   = Company::where('is_active', true)->count();
        $companiesInactive = Company::where('is_active', false)->count();

        // Employés (global & période)
        $employeesTotal = Employee::count();

        $base = SalaryAdvance::with(['payout','company'])
            ->whereBetween('created_at', [$from, $to]);

        $employeesWithRequests = (clone $base)->distinct('employee_id')->count('employee_id');

        // Période : demandes & montants (tous statuts)
        $requestsCount  = (clone $base)->count();
        $totalRequested = (int) ((clone $base)->sum('amount_final')); // demandes + frais (tous statuts)

        // Succès uniquement (réalisé)
        $successBase      = (clone $base)->whereHas('payout', fn($q) => $q->where('status','success'));
        $employeesPaid    = (clone $successBase)->distinct('employee_id')->count('employee_id');
        $totalDueSuccess  = (int) ((clone $successBase)->sum('amount_final')); // dû réel (demandes + frais)
        $marginFees       = (int) ((clone $successBase)->sum('fee_fixed'));    // marge réalisée (frais)

        // Top 5 entreprises par nb de demandes (tous statuts)
        $topCounts = (clone $base)
            ->select('company_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('company_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->pluck('cnt','company_id'); // [company_id => cnt]

        $topIds = $topCounts->keys()->all();

        // Totaux "tous statuts" sur ces entreprises
        $topAll = (clone $base)
            ->whereIn('company_id', $topIds)
            ->select('company_id',
                DB::raw('SUM(amount_final) as total_all'),
                DB::raw('SUM(fee_fixed) as fees_all'),
                DB::raw('COUNT(*) as cnt_all')
            )
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        // Totaux "succès" sur ces entreprises
        $topSuccess = (clone $successBase)
            ->whereIn('company_id', $topIds)
            ->select('company_id',
                DB::raw('SUM(amount_final) as due_success'),
                DB::raw('SUM(fee_fixed) as fees_success'),
                DB::raw('COUNT(*) as success_cnt')
            )
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $companyNames = Company::whereIn('id', $topIds)->pluck('name','id');

        $topCompanies = collect();
        foreach ($topCounts as $companyId => $cnt) {
            $all = $topAll->get($companyId);
            $suc = $topSuccess->get($companyId);
            $topCompanies->push((object)[
                'company_id'   => $companyId,
                'name'         => $companyNames[$companyId] ?? '—',
                'requests'     => (int)($all->cnt_all ?? $cnt ?? 0),
                'total_all'    => (int)($all->total_all ?? 0),
                'fees_all'     => (int)($all->fees_all ?? 0),
                'success_cnt'  => (int)($suc->success_cnt ?? 0),
                'due_success'  => (int)($suc->due_success ?? 0),
                'fees_success' => (int)($suc->fees_success ?? 0),
            ]);
        }

        // Étiquette période
        $periodLabel = Carbon::parse($start)->format('d/m/Y').' → '.Carbon::parse($end)->format('d/m/Y');

        return view('admin.dashboard', compact(
            'start','end','periodLabel',
            'companiesActive','companiesInactive',
            'employeesTotal','employeesWithRequests','employeesPaid',
            'requestsCount','totalRequested','totalDueSuccess','marginFees',
            'topCompanies'
        ));
    }
}
