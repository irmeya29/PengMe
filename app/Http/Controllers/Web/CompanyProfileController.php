<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function show()
    {
        return view('company.profile');
    }

    public function update(Request $request)
    {
        $company = auth('web')->user();

        $data = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','email','max:255', Rule::unique('companies','email')->ignore($company->id)],
            'phone'   => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
        ]);

        $company->fill($data)->save();

        return back()->with('ok','Profil mis à jour.');
    }

    public function updateLogo(Request $request)
    {
        $company = auth('web')->user();

        $request->validate([
            'logo' => ['required','image','mimes:png,jpg,jpeg','max:2048'],
        ]);

        // Dossier de stockage
        $folder = 'logo_company';

        // Créer le dossier s’il n’existe pas
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        // Supprimer ancien logo s’il existe
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }

        // Nom unique → company_ID_timestamp.ext
        $filename = 'company_'.$company->id.'_'.time().'.'.$request->file('logo')->getClientOriginalExtension();

        // Sauvegarde
        $path = $request->file('logo')->storeAs($folder, $filename, 'public');

        $company->logo_path = $path; // ex: logo_company/company_1_1700000000.png
        $company->save();

        return back()->with('ok','Logo mis à jour.');
    }

    public function updatePassword(Request $request)
    {
        $company = auth('web')->user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required','confirmed','min:8'],
        ]);

        if (! Hash::check($request->current_password, $company->password)) {
            return back()->withErrors('Mot de passe actuel invalide.');
        }

        $company->password = Hash::make($request->password);
        $company->save();

        return back()->with('ok','Mot de passe modifié.');
    }
}
