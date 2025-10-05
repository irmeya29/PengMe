<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeProfileController extends Controller
{
    public function show(Request $request){
        $e = $request->user(); // Employee via sanctum
        return [
            'first_name'=>$e->first_name,
            'last_name'=>$e->last_name,
            'email'=>$e->email,
            'phone'=>$e->phone,
            'monthly_salary'=>$e->monthly_salary,
            'eligible'=>$e->eligible,
        ];
    }

    public function update(Request $request){
        $e = $request->user();
        $data = $request->validate([
            'email'=>'nullable|email',
            'phone'=>'nullable|string|max:30',
            'password'=>'nullable|string|min:8'
        ]);

        // Mise à jour générale (optionnelle si tu veux permettre de changer son profil plus tard)
        if(isset($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }

        $e->update($data);
        return ['message'=>'Profil mis à jour'];
    }

    // 🚀 Nouveau endpoint : définir le mot de passe pour la première fois
    public function setPassword(Request $request){
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
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
