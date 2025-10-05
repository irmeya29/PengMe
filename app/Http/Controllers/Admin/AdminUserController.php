<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $s = trim((string) $request->get('search'));
        $q = Admin::query();

        if ($s) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                   ->orWhere('email', 'like', "%$s%");
            });
        }

        $admins = $q->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('admins', 's'));
    }

    public function create()
    {
        $admin = new Admin();
        return view('admin.users.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150','unique:admins,email'],
            'password' => ['required','string','min:8','confirmed'],
            'active'   => ['required','in:0,1'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['active']   = $request->boolean('active');

        Admin::create($data);

        return redirect()->route('admin.users.index')->with('ok', 'Admin créé avec succès.');
    }

    public function edit(Admin $admin)
    {
        return view('admin.users.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150', Rule::unique('admins','email')->ignore($admin->id)],
            'password' => ['nullable','string','min:8','confirmed'],
            'active'   => ['required','in:0,1'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Sécurité : ne pas se désactiver soi-même
        if ($admin->id === auth('admin')->id() && !$request->boolean('active')) {
            return back()->withErrors('Vous ne pouvez pas désactiver votre propre compte.');
        }

        $data['active'] = $request->boolean('active');

        $admin->update($data);

        return redirect()->route('admin.users.index')->with('ok', 'Admin mis à jour.');
    }

    public function destroy(Admin $admin)
    {
        // Sécurité : pas d’auto-suppression, ni suppression du dernier admin actif
        if ($admin->id === auth('admin')->id()) {
            return back()->withErrors('Vous ne pouvez pas supprimer votre propre compte.');
        }
        $activeCount = Admin::where('active', true)->count();
        if ($admin->active && $activeCount <= 1) {
            return back()->withErrors('Impossible de supprimer le dernier admin actif.');
        }

        $admin->delete();

        return back()->with('ok', 'Admin supprimé.');
    }

    public function toggle(Admin $admin)
    {
        // Sécurité
        if ($admin->id === auth('admin')->id()) {
            return back()->withErrors('Vous ne pouvez pas désactiver/activer votre propre compte.');
        }
        if ($admin->active && Admin::where('active', true)->count() <= 1) {
            return back()->withErrors('Impossible de désactiver le dernier admin actif.');
        }

        $admin->active = ! $admin->active;
        $admin->save();

        return back()->with('ok', $admin->active ? 'Admin activé.' : 'Admin désactivé.');
    }
}
