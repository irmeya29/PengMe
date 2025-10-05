<?php

return [

    'menu' => [

        // Section navigation
        [
            'text' => 'Navigation',
            'is_header' => true
        ],
        [
            'url'  => '/dashboard',
            'icon' => 'fa fa-laptop',
            'text' => 'Dashboard'
        ],
        [
            'url'  => '/employees',
            'icon' => 'fa fa-users',
            'text' => 'Employés'
        ],
        [
            'url'  => '/employees-import',
            'icon' => 'fa fa-file-upload',
            'text' => 'Import CSV'
        ],
        [
            'url'  => '/advances',
            'icon' => 'fa fa-coins',
            'text' => 'Avances sur salaire'
        ],

        [ 'is_divider' => true ],

        // Section utilisateur
        [
            'text' => 'Mon compte',
            'is_header' => true
        ],
        [
            'url'  => '/profile',
            'icon' => 'fa fa-user-circle',
            'text' => 'Profil'
        ],
        [
            'url'  => '/settings',
            'icon' => 'fa fa-cog',
            'text' => 'Paramètres'
        ],
        
    ]
];
