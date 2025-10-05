<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'company_id','matricule','first_name','last_name','email','phone',
        'monthly_salary','employee_code','eligible','password'
    ];

    protected $hidden = ['password','remember_token'];

    // ✅ Casts pour assurer des types corrects dans le JSON
    protected $casts = [
        'eligible' => 'boolean',
        'monthly_salary' => 'integer',
    ];

    public function company() { 
        return $this->belongsTo(Company::class); 
    }

    public function advances() { 
        return $this->hasMany(SalaryAdvance::class); 
    }
}

