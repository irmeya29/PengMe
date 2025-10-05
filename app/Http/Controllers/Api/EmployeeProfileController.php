<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeProfileController extends Controller
{
    /**
     * 🧾 Affiche le profil de l’employé connecté
     */
    public function show(Request $request)
    {
        $e = $request->user(); // Employé connecté via Sanctum

        return [
            'first_name'       => $e->first_name,
            'last_name'        => $e->last_name,
            'email'            => $e->email,
            'phone'            => $e->phone,
            'monthly_salary'   => $e->monthly_salary,
            'eligible'         => (bool) $e->eligible,

            // ✅ Bloc entreprise ajouté
            'company' => [
                'id'   => $e->company->id ?? null,
                'name' => $e->company->name ?? 'Entreprise inconnue',
                'code' => $e->company->code ?? null,
            ],
        ];
    }

    /**
     * ✏️ Met à jour le profil de l’employé (email, téléphone, mot de passe)
     */
    public function update(Request $request)
    {
        $e = $request->user();

        $data = $request->validate([
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
        ]);

        // Hash du mot de passe si fourni
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $e->update($data);

        return ['message' => 'Profil mis à jour'];
    }

    /**
     * 🔐 Permet de définir un mot de passe pour la première fois
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $e = $request->user();

        if (!is_null($e->password)) {
            return response()->json(['message' => 'Mot de passe déjà défini'], 400);
        }

        $e->password = Hash::make($request->password);
        $e->save();

        return ['message' => 'Mot de passe défini avec succès'];
    }
}
