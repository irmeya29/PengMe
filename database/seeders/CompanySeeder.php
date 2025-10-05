<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder {
  public function run(): void {
    $company = Company::firstOrCreate(
      ['email'=>'acme@corp.test'],
      [
        'name'=>'ACME SARL',
        'password'=>Hash::make('password'),
        'code'=>Str::upper(Str::random(6)),
        'is_active'=>true,
      ]
    );

    Employee::firstOrCreate(
      ['company_id'=>$company->id,'matricule'=>'EMP001'],
      [
        'first_name'=>'Ibrahim','last_name'=>'Zongo',
        'email'=>'emp@acme.test','monthly_salary'=>200000,
        'eligible'=>true,'password'=>Hash::make('password')
      ]
    );
  }
}
