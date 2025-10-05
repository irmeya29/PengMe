<?php

// app/Http/Controllers/Web/CompanyAuthController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyAuthController extends Controller
{
    /** Affiche le formulaire d'inscription entreprise */
    public function showRegister()
    {
        return view('company.auth.register');
    }

    /** Traite l'inscription entreprise */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:150'],
            'rccm'     => ['nullable','string','max:100'],
            'ifu'      => ['nullable','string','max:100'],
            'email'    => ['required','email','unique:companies,email'],
            'phone'    => ['required','string','max:50'],      // ← ajouté
            'address'  => ['nullable','string','max:255'],     // ← ajouté
            'password' => ['required','string','min:8','confirmed'],
        ]);

        // Génère un code entreprise unique (6 caractères)
        do {
            $code = Str::upper(Str::random(6));
        } while (Company::where('code', $code)->exists());

        // Création
        $company = Company::create([
            'name'      => $data['name'],
            'rccm'      => $data['rccm']    ?? null,
            'ifu'       => $data['ifu']     ?? null,
            'email'     => $data['email'],
            'phone'     => $data['phone']   ?? null,
            'address'   => $data['address'] ?? null,
            'password'  => Hash::make($data['password']),
            'code'      => $code,
            'is_active' => true,
            'logo_path' => null,
        ]);

        // Connexion auto
        Auth::guard('web')->login($company);
        $request->session()->regenerate();

        return redirect()
            ->route('company.dashboard')
            ->with('ok', "Bienvenue ! Code-entreprise : {$company->code}");
    }

    /** Affiche le formulaire de connexion */
    public function showLogin()
    {
        return view('company.auth.login');
    }

    /** Traite la connexion */
    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::guard('web')->attempt($cred, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Vérifie si l'entreprise est active
            /** @var \App\Models\Company $user */
            $user = Auth::guard('web')->user();
            if (! $user->is_active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors('Compte suspendu. Contactez le support.');
            }

            return redirect()->route('company.dashboard');
        }

        return back()->withErrors('Identifiants invalides.');
    }

    /** Déconnexion */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('company.login'); // ← corrigé
    }
}
