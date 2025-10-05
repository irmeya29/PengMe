<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['salary_advance_id','method','status','reference','meta'];
    protected $casts = ['meta' => 'array'];

    public function advance(){ return $this->belongsTo(SalaryAdvance::class,'salary_advance_id'); }
}
