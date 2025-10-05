<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth('web')->id();
        $q = Employee::where('company_id', $companyId);

        if ($s = trim((string)$request->get('search'))) {
            $q->where(function($x) use ($s) {
                $x->where('matricule','like',"%$s%")
                  ->orWhere('first_name','like',"%$s%")
                  ->orWhere('last_name','like',"%$s%")
                  ->orWhere('email','like',"%$s%")
                  ->orWhere('phone','like',"%$s%");
            });
        }

        $employees = $q->latest('id')->paginate(15)->withQueryString();

        return view('company.employees.index', compact('employees','s'));
    }

    public function create()
    {
        return view('company.employees.create');
    }

    public function store(Request $request)
    {
        $companyId = auth('web')->id();

        $data = $request->validate([
            'matricule'       => ['required','string','max:50',
                Rule::unique('employees','matricule')->where('company_id',$companyId)],
            'first_name'      => ['required','string','max:80'],
            'last_name'       => ['required','string','max:80'],
            'email'           => ['nullable','email','unique:employees,email'],
            'phone'           => ['nullable','string','max:30','unique:employees,phone'],
            'monthly_salary'  => ['required','integer','min:1'],
            'employee_code'   => ['nullable','string','max:50'],
            'eligible'        => ['nullable','boolean'],
        ]);

        Employee::create([
            ...$data,
            'company_id' => $companyId,
            'eligible'   => $request->boolean('eligible'),
            'password'   => null, // ⚡ mot de passe défini plus tard par l’employé
        ]);

        return redirect()->route('employees.index')->with('ok','Employé créé');
    }

    public function edit(Employee $employee)
    {
        $this->authorizeBelongsToCompany($employee);
        return view('company.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeBelongsToCompany($employee);
        $companyId = auth('web')->id();

        $data = $request->validate([
            'matricule'       => ['required','string','max:50',
                Rule::unique('employees','matricule')->where('company_id',$companyId)->ignore($employee->id)],
            'first_name'      => ['required','string','max:80'],
            'last_name'       => ['required','string','max:80'],
            'email'           => ['nullable','email','unique:employees,email,'.$employee->id],
            'phone'           => ['nullable','string','max:30','unique:employees,phone,'.$employee->id],
            'monthly_salary'  => ['required','integer','min:1'],
            'employee_code'   => ['nullable','string','max:50'],
            'eligible'        => ['nullable','boolean'],
        ]);

        $data['eligible'] = $request->boolean('eligible');

        // ⚡ On ne touche pas au mot de passe ici
        unset($data['password']);

        $employee->update($data);

        return redirect()->route('employees.index')->with('ok','Employé mis à jour');
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeBelongsToCompany($employee);
        $employee->delete();
        return back()->with('ok','Employé supprimé');
    }

    public function showImport()
    {
        return view('company.employees.import');
    }

    public function import(Request $request)
    {
        $companyId = auth('web')->id();

        $request->validate([
            'file' => ['required','file','mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if (!$handle) return back()->withErrors('Fichier illisible.');

        // En-tête attendu
        // matricule,first_name,last_name,email,phone,monthly_salary,employee_code,eligible
        $header = fgetcsv($handle, 0, ',');
        if (!$header) return back()->withErrors('CSV vide ou sans en-tête.');

        $header = array_map(fn($h)=>strtolower(trim($h)), $header);
        $count = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $data = array_combine($header, array_map('trim', $row));
            if (!$data || empty($data['matricule'])) continue;

            $emp = Employee::firstOrNew([
                'company_id' => $companyId,
                'matricule'  => $data['matricule'],
            ]);

            $emp->first_name     = $data['first_name'] ?? '';
            $emp->last_name      = $data['last_name'] ?? '';
            $emp->email          = $data['email'] ?: null;
            $emp->phone          = $data['phone'] ?: null;
            $emp->monthly_salary = (int)($data['monthly_salary'] ?? 0);
            $emp->employee_code  = $data['employee_code'] ?? null;
            $emp->eligible       = !empty($data['eligible']) && in_array(strtolower($data['eligible']), ['1','true','yes','oui']);

            if (!$emp->exists) {
                $emp->password = null; // ⚡ toujours null => employé définira plus tard
            }

            $emp->save();
            $count++;
        }
        fclose($handle);

        return back()->with('ok', "Import terminé : {$count} lignes traitées.");
    }

    private function authorizeBelongsToCompany(Employee $employee): void
    {
        abort_if($employee->company_id !== auth('web')->id(), 403);
    }
}
