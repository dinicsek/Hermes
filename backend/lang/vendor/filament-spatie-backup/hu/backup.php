<?php

return [

    'components' => [
        'backup_destination_list' => [
            'table' => [
                'actions' => [
                    'download' => 'Letöltés',
                    'delete' => 'Törlés',
                ],

                'fields' => [
                    'path' => 'Elérési út',
                    'disk' => 'Tárhely',
                    'date' => 'Dátum',
                    'size' => 'Méret',
                ],

                'filters' => [
                    'disk' => 'Tárhely',
                ],
            ],
        ],

        'backup_destination_status_list' => [
            'table' => [
                'fields' => [
                    'name' => 'Név',
                    'disk' => 'Tárhely',
                    'healthy' => 'Egészséges',
                    'amount' => 'Menynyiség',
                    'newest' => 'Legújabb',
                    'used_storage' => 'Használt tárhely',
                ],
            ],
        ],
    ],

    'pages' => [
        'backups' => [
            'actions' => [
                'create_backup' => 'Biztonsági mentés készítése',
            ],

            'heading' => 'Biztonsági mentések',

            'messages' => [
                'backup_success' => 'Új biztonsági mentés készítése a háttérben.',
                'backup_delete_success' => 'Biztonsági mentés törlése a háttérben.',
            ],

            'modal' => [
                'buttons' => [
                    'only_db' => 'Csak DB',
                    'only_files' => 'Csak fájlok',
                    'db_and_files' => 'DB & Fájlok',
                ],

                'label' => 'Kérlek válassz egy lehetőséget',
            ],

            'navigation' => [
                'group' => 'Rendszer',
                'label' => 'Biztonsági mentések',
            ],
        ],
    ],

];
