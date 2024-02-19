<?php

return [
    'exception_message' => 'Exception message: :message',
    'exception_trace' => 'Exception trace: :trace',
    'exception_message_title' => 'Exception message',
    'exception_trace_title' => 'Exception trace',

    'backup_failed_subject' => ':application_name - Sikertelen biztonsági mentés',
    'backup_failed_body' => 'Egy hiba történt a(z) :application_name alkalmazás biztonsági mentése közben',

    'backup_successful_subject' => ':application_name - Sikeres biztonsági mentés',
    'backup_successful_subject_title' => 'Sikeres biztonsági mentés!',
    'backup_successful_body' => 'A(z) :application_name alkalmazáshoz sikeresen létrejött egy biztonsági mentés a(z) :disk_name nevű tárhelyen.',

    'cleanup_failed_subject' => ':application_name - Sikertelen törlése a biztonsági mentéseknek',
    'cleanup_failed_body' => 'Egy hiba történt a(z) :application_name alkalmazás biztonsági mentéseinek törlése közben.',

    'cleanup_successful_subject' => ':application_name - Sikeres törlése a biztonsági mentéseknek',
    'cleanup_successful_subject_title' => 'A biztonsági mentések törlése sikeres volt!',
    'cleanup_successful_body' => 'A(z) :application_name alkalmazás biztonsági mentései sikeresen törölve lettek a(z) :disk_name nevű tárhelyről.',

    'healthy_backup_found_subject' => ':application_name - A biztonsági mentések egészségesek',
    'healthy_backup_found_subject_title' => 'A biztonsági mentések egészségesek a(z) :application_name alkalmazáshoz. Jó munka!',
    'healthy_backup_found_body' => 'A(z) :application_name alkalmazás biztonsági mentései egészségesek.',

    'unhealthy_backup_found_subject' => ':application_name - A biztonsági mentések egészségtelenek',
    'unhealthy_backup_found_subject_title' => 'Fontos: a(z) :application_name biztonsági mentései nem egészségesek. :problem',
    'unhealthy_backup_found_body' => 'A(z) :application_name alkalmazás biztonsági mentései a :disk_name nevű tárhelyen nem egészségesek.',
    'unhealthy_backup_found_not_reachable' => 'A tárhely nem elérhető. :error',
    'unhealthy_backup_found_empty' => 'Az alkalmazásnak nincsenek biztonsági mentései.',
    'unhealthy_backup_found_old' => 'A legújabb biztonsági mentés (:date) túl régi.',
    'unhealthy_backup_found_unknown' => 'Ismeretlen hiba történt.',
    'unhealthy_backup_found_full' => 'A tárhely megtelt. A biztonsági mentések mérete (:disk_usage) meghaladja a tárhelyhez beállított maximális méretet (:disk_limit).',

    'no_backups_info' => 'Nincsenek biztonsági mentések',
    'application_name' => 'Alkalmazás neve',
    'backup_name' => 'Biztonsági mentés neve',
    'disk' => 'Tárhely',
    'newest_backup_size' => 'Legújabb biztonsági mentés mérete',
    'number_of_backups' => 'Biztonsági mentések száma',
    'total_storage_used' => 'Használt tárhely mérete',
    'newest_backup_date' => 'Legújabb biztonsági mentés dátuma',
    'oldest_backup_date' => 'Legrégebbi biztonsági mentés dátuma',
];
