<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'), // par défaut on garde web = entreprise
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        // Entreprises (espace web avec session)
        'web' => [
            'driver' => 'session',
            'provider' => 'companies',
        ],

        // Admins (dashboard avec session)
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        // Employés (API mobile avec Sanctum)
        'employee-token' => [
            'driver' => 'sanctum',
            'provider' => 'employees',
        ],
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'companies' => [
            'driver' => 'eloquent',
            'model' => App\Models\Company::class,
        ],

        'employees' => [
            'driver' => 'eloquent',
            'model' => App\Models\Employee::class,
        ],
    ],

    'passwords' => [
        // reset mot de passe pour entreprises
        'companies' => [
            'provider' => 'companies',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // reset mot de passe pour employés
        'employees' => [
            'provider' => 'employees',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // reset mot de passe pour admins
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
