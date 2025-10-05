<?php
return [
    'menu' => [
        ['text'=>'Administration','is_header'=>true],

        ['url'=>'/admin/dashboard','icon'=>'fa fa-tachometer-alt','text'=>'Dashboard'],

        ['url'=>'/admin/companies','icon'=>'fa fa-building','text'=>'Entreprises'],

        ['url'=>'/admin/users','icon'=>'fa fa-user-shield','text'=>'Admins'], // optionnel

        ['is_divider'=>true],

        ['text'=>'Paramètres','is_header'=>true],

        
    ]
];
