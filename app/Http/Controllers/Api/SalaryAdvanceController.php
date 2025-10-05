<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalaryAdvanceController extends Controller
{
    public function index(Request $request){
        $e = $request->user();
        return SalaryAdvance::where('employee_id',$e->id)
            ->latest()->paginate(10);
    }

    public function store(Request $request){
        $e = $request->user();
        $data = $request->validate([
            'amount_requested'=>'required|integer|min:10000|max:50000',
            'pay_method'=>'required|in:orange_money'
        ]);

        // cap salaire/4
        $cap = intdiv($e->monthly_salary, 4);
        if ($data['amount_requested'] > $cap) {
            return response()->json(['message'=>"Plafond dépassé (max {$cap} FCFA)"], 422);
        }

        $fee = (int) config('pengme.fee_fixed', 2000);
        $amount_final = $data['amount_requested'] + $fee;

        $advance = SalaryAdvance::create([
            'company_id' => $e->company_id,
            'employee_id'=> $e->id,
            'amount_requested'=>$data['amount_requested'],
            'fee_fixed'=>$fee,
            'amount_final'=>$amount_final,
            'total_repayable'=>$amount_final,
            'status' => $e->eligible ? 'approved' : 'pending',
            'meta' => ['pay_method'=>$data['pay_method']],
        ]);

        // Simulation payout OM si approuvé automatiquement
        if ($e->eligible) {
            $payout = Payout::create([
                'salary_advance_id'=>$advance->id,
                'method'=>'orange_money',
                'status'=>'pending',
                'reference'=>null,
                'meta'=>['simulate'=>true],
            ]);
            // MVP: succès immédiat
            $payout->update([
                'status'=>'success',
                'reference'=>'OM-'.Str::upper(Str::random(10))
            ]);
        }

        return [
          'message' => $e->eligible ? 'Demande auto-approuvée' : 'Demande créée (en attente)',
          'advance' => $advance->load('payout')
        ];
    }
}
