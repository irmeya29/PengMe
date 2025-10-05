<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Company extends Authenticatable
{
    use Notifiable;

   protected $fillable = [
  'name','rccm','ifu','email','password','code','phone','address','logo_path','is_active'
];


    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employees(){ return $this->hasMany(Employee::class); }

    // --- Compatibilité : permet d'utiliser $company->active partout ---
    public function getActiveAttribute(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? false);
    }
    public function setActiveAttribute($value): void
    {
        $this->attributes['is_active'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
