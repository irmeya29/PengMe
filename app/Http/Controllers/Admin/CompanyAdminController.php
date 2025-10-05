<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyAdminController extends Controller
{
    public function index(Request $request)
    {
        $s = trim((string)$request->get('search'));
        $q = Company::query();

        if ($s) {
            $q->where(function($qq) use ($s) {
                $qq->where('name','like',"%$s%")
                   ->orWhere('email','like',"%$s%")
                   ->orWhere('code','like',"%$s%");
            });
        }

        $companies = $q->latest('id')->paginate(20)->withQueryString();
        return view('admin.companies.index', compact('companies','s'));
    }

    public function toggle(Company $company)
    {
        $company->is_active = ! $company->is_active;
        $company->save();

        return back()->with('ok', $company->is_active ? 'Entreprise activée' : 'Entreprise suspendue');
    }

    /** Impersonation admin → entreprise */
    public function impersonate(Company $company)
    {
        // sécurité : uniquement si un admin est connecté
        abort_unless(auth('admin')->check(), 403, 'Accès non autorisé');

        // option : empêcher d’impersoner une entreprise suspendue
        if (!$company->is_active) {
            return back()->withErrors('Cette entreprise est suspendue.');
        }

        // mémoriser l’admin pour retour + démarrage de session d’impersonation
        session([
            'impersonate_admin_id'   => auth('admin')->id(),
            'impersonate_started_at' => now(),
        ]);

        // connexion "web" en tant que l’entreprise
        auth('web')->login($company);

        return redirect()
            ->route('company.dashboard')
            ->with('ok', 'Connecté comme '.$company->name);
    }

    /** Arrêt impersonation */
    public function stopImpersonate()
    {
        if (session()->has('impersonate_admin_id')) {
            // sortir du compte entreprise (guard web)
            auth('web')->logout();

            // récupérer l’admin initial
            $adminId = session('impersonate_admin_id');

            // nettoyer la session
            session()->forget(['impersonate_admin_id','impersonate_started_at']);

            // reconnecter l’admin
            auth('admin')->loginUsingId($adminId);
        }

        return redirect()
            ->route('admin.companies.index')
            ->with('ok','Impersonation terminée.');
    }
}
