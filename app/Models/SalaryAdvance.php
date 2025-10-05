<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    protected $fillable = [
      'company_id','employee_id','amount_requested','fee_fixed',
      'amount_final','total_repayable','status','meta'
    ];
    protected $casts = ['meta' => 'array'];

    public function company(){ return $this->belongsTo(Company::class); }
    public function employee(){ return $this->belongsTo(Employee::class); }
    public function payout(){ return $this->hasOne(Payout::class); }
}
