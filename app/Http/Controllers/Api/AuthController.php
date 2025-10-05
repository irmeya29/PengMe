<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Login Employé (email ou téléphone + code entreprise).
     * - Si password NULL => renvoie must_create_password + temp_token (Sanctum)
     * - Sinon => vérifie le mot de passe et renvoie un token d’accès
     */
    public function loginEmployee(Request $request)
    {
        $cred = $request->validate([
            'login'        => ['required', 'string'], // email OU phone
            'company_code' => ['required', 'string', Rule::exists('companies', 'code')],
            'password'     => ['nullable', 'string'], // absent/NULL au premier login
        ]);

        // 1) Entreprise
        $company = Company::where('code', $cred['company_code'])->first();
        if (!$company || !$company->is_active) {
            return response()->json(['message' => 'Entreprise inactive ou inconnue'], 403);
        }

        // 2) Employé (email OU phone), restreint à l’entreprise
        $login = $cred['login'];
        $employee = Employee::where('company_id', $company->id)
            ->where(function ($q) use ($login) {
                $q->where('email', $login)
                  ->orWhere('phone', $login);
            })
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employé non trouvé'], 404);
        }

        // (Optionnel) Si tu veux bloquer l’app pour les non éligibles au MVP
        // if (!$employee->eligible) {
        //     return response()->json(['message' => "Contactez votre entreprise (non éligible)."], 403);
        // }

        // 3) Mot de passe pas encore créé -> forcer la création
        if (is_null($employee->password)) {
            $tempToken = $employee->createToken('set-password')->plainTextToken;

            return response()->json([
                'must_create_password' => true,
                'message' => 'Vous devez définir un mot de passe.',
                'temp_token' => $tempToken, // Utilisé avec POST /api/employee/set-password
                'employee' => [
                    'id'   => $employee->id,
                    'name' => trim($employee->first_name . ' ' . $employee->last_name),
                    'eligible' => (bool) $employee->eligible,
                ],
                'company' => [
                    'id'   => $company->id,
                    'code' => $company->code,
                    'name' => $company->name,
                ],
            ], 200);
        }

        // 4) Login normal avec password
        if (empty($cred['password']) || !Hash::check($cred['password'], $employee->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 422);
        }

        $accessToken = $employee->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $accessToken,
            'employee' => [
                'id'       => $employee->id,
                'name'     => trim($employee->first_name . ' ' . $employee->last_name),
                'eligible' => (bool) $employee->eligible,
            ],
            'company' => [
                'id'   => $company->id,
                'code' => $company->code,
                'name' => $company->name,
            ],
        ], 200);
    }
}
