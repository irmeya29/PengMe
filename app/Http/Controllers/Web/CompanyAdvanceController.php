<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompanyAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth('web')->id();

        // Récupérer période
        $start = $request->get('start');
        $end   = $request->get('end');

        // ⚡ Si aucune période précisée → mois en cours par défaut
        if (!$start && !$end) {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end   = Carbon::now()->endOfMonth()->toDateString();
            $request->merge(['start'=>$start,'end'=>$end]);
        }

        $q = SalaryAdvance::with(['employee','payout'])
            ->where('company_id', $companyId)
            ->when($start, fn($query)=>$query->whereDate('created_at','>=',$start))
            ->when($end,   fn($query)=>$query->whereDate('created_at','<=',$end));

        // 🔍 Filtres
        if ($s = trim((string)$request->get('search'))) {
            $q->whereHas('employee', function($x) use ($s) {
                $x->where('matricule','like',"%$s%")
                  ->orWhere('first_name','like',"%$s%")
                  ->orWhere('last_name','like',"%$s%")
                  ->orWhere('email','like',"%$s%");
            });
        }
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        if ($payout = $request->get('payout_status')) {
            $q->whereHas('payout', fn($x)=>$x->where('status',$payout));
        }

        $advances = $q->latest('id')->paginate(15)->withQueryString();

        return view('company.advances.index', [
            'advances'=>$advances,
            's'=>$request->get('search'),
            'status'=>$request->get('status'),
            'payout'=>$request->get('payout_status'),
        ]);
    }

    public function show(SalaryAdvance $advance)
    {
        $this->authorizeBelongsToCompany($advance);
        $advance->load(['employee','payout']);
        return view('company.advances.show', compact('advance'));
    }

    public function markPayoutSuccess(SalaryAdvance $advance)
    {
        $this->authorizeBelongsToCompany($advance);
        if ($advance->payout) {
            $advance->payout->update([
                'status'=>'success',
                'meta'=>array_merge($advance->payout->meta??[],[
                    'manual_update'=>true,
                    'marked_at'=>now()->toDateTimeString()
                ])
            ]);
        }
        return back()->with('ok','✅ Payout marqué en succès.');
    }

    public function markPayoutFailed(SalaryAdvance $advance)
    {
        $this->authorizeBelongsToCompany($advance);
        if ($advance->payout) {
            $advance->payout->update([
                'status'=>'failed',
                'meta'=>array_merge($advance->payout->meta??[],[
                    'manual_update'=>true,
                    'marked_at'=>now()->toDateTimeString()
                ])
            ]);
        }
        return back()->with('ok','❌ Payout marqué en échec.');
    }

    private function authorizeBelongsToCompany(SalaryAdvance $advance): void
    {
        abort_if($advance->company_id !== auth('web')->id(), 403);
    }
}
