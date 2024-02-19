<?php

return [

    'labels' => [
        'model' => 'Hiba',
        'model_plural' => 'Hibák',
        'navigation' => 'Hibák',
        'navigation_group' => 'Rendszer',

        'tabs' => [
            'exception' => 'Hiba',
            'headers' => 'Header adatok',
            'cookies' => 'Sütik',
            'body' => 'Kérvény body',
            'queries' => 'Adatbázis lekérések',
        ],
    ],

    'empty_list' => 'Hurrá! Nincsenek hibák 😎',

    'columns' => [
        'method' => 'HTTP method',
        'path' => 'Elérési út',
        'type' => 'Típus',
        'code' => 'Kód',
        'ip' => 'IP',
        'occurred_at' => 'Bekövetkezett',
    ],

];
