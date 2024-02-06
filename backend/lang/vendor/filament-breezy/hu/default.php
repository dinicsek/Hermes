<?php

return [
    'login' => [
        'username_or_email' => 'Felhasználónév vagy e-mail cím',
        'forgot_password_link' => 'Elfelejtetted a jelszavad?',
        'create_an_account' => 'fiók létrehozása',
    ],
    'password_confirm' => [
        'heading' => 'Jelszó megerősítése',
        'description' => 'Kérem add meg a jelszavad, hogy vérehajthasd ezt a műveletet.',
        'current_password' => 'Jelenlegi jelszó',
    ],
    'two_factor' => [
        'heading' => 'Kétfaktoros hitelesítés',
        'description' => 'Kérjük, add meg a fiókodhoz való hozzáféréshez a hitelesítő alkalmazás által biztosított kódot.',
        'code_placeholder' => 'XXX-XXX',
        'recovery' => [
            'heading' => 'Kétfaktoros hitelesítés visszaállítása',
            'description' => 'Kérjük, add meg a fiókodhoz való hozzáféréshez a helyreállítási kódok egyikét.',
        ],
        'recovery_code_placeholder' => 'abcdef-98765',
        'recovery_code_text' => 'Elveszetted a készüléked?',
        'recovery_code_link' => 'Használj helyreállítási kódot',
        'back_to_login_link' => 'Vissza a bejelentkezéshez',
    ],
    'registration' => [
        'title' => 'Regisztráció',
        'heading' => 'Fiók létrehozása',
        'submit' => [
            'label' => 'Regisztráció',
        ],
        'notification_unique' => 'Már létezik fiók ezzel az e-mail-címmel. Kérjük jelentkezz be.',
    ],
    'reset_password' => [
        'title' => 'Elfelejtett jelszó',
        'heading' => 'Jelszó visszaállítása',
        'submit' => [
            'label' => 'Küldés',
        ],
        'notification_error' => 'Hiba: próbáld újra később.',
        'notification_error_link_text' => 'Újra próbálás',
        'notification_success' => 'Nézd meg a beérkezett e-maileid az utasításokért!',
    ],
    'verification' => [
        'title' => 'E-mail megerősítés',
        'heading' => 'E-mail ellenőrzés szükséges',
        'submit' => [
            'label' => 'Kijelentkezés',
        ],
        'notification_success' => 'Nézd meg a beérkezett e-maileid az utasításokért!',
        'notification_resend' => 'Az ellenőrző e-mailt újra elküldtük.',
        'before_proceeding' => 'Mielőtt folytatnád, kérjük, ellenőrizd az e-mail fiókodban az ellenőrző linket.',
        'not_receive' => 'Ha nem kaptad meg az e-mailt,',
        'request_another' => 'kattints ide egy másik kéréséhez',
    ],
    'profile' => [
        'account' => 'Fiók',
        'profile' => 'Profil',
        'my_profile' => 'Saját profil',
        'personal_info' => [
            'heading' => 'Személyes információk',
            'subheading' => 'Kérjük, add meg a profilod adataid',
            'submit' => [
                'label' => 'Frissítés',
            ],
            'notify' => 'A profil sikeresen frissítve!',
        ],
        'password' => [
            'heading' => 'Jelszó',
            'subheading' => 'Legalább 8 karakterből kell állnia.',
            'submit' => [
                'label' => 'Frissítés',
            ],
            'notify' => 'A jelszó sikeresen frissítve!',
        ],
        '2fa' => [
            'title' => 'Kétfaktoros hitelesítés',
            'description' => 'Kétfaktoros hitelesítés kezelése fiókodhoz (ajánlott).',
            'actions' => [
                'enable' => 'Engedélyezés',
                'regenerate_codes' => 'Új kódok generálása',
                'disable' => 'Tiltás',
                'confirm_finish' => 'Megerősítés és befejezés',
                'cancel_setup' => 'A beállítás megszakítása',
            ],
            'setup_key' => 'Beállítási kulcs',
            'not_enabled' => [
                'title' => 'Nem engedélyezted a kétfaktoros hitelesítést.',
                'description' => 'Ha a kétfaktoros hitelesítés engedélyezve van, a rendszer a hitelesítés során egy biztonságos, véletlenszerű tokent kér. Ezt a tokent telefonod Google Authenticator alkalmazásából kérheted le.',
            ],
            'finish_enabling' => [
                'title' => 'Kétfaktoros hitelesítés engedélyezésének befejezése.',
                'description' => 'A kétfaktoros hitelesítés engedélyezésének befejezéséhez olvasd be a következő QR-kódot a telefonod hitelesítő alkalmazásával, vagy add meg a beállítási kulcsot, és add meg az ez után generált egyszeri kódot.',
            ],
            'enabled' => [
                'title' => 'Engedélyezted a kétfaktoros hitelesítést!',
                'description' => 'A kétfaktoros hitelesítés most engedélyezve van. Olvasd be a következő QR-kódot a telefonod hitelesítő alkalmazásával, vagy írd be a beállítási kulcsot.',
                'store_codes' => 'Tárold ezeket a helyreállítási kódokat egy biztonságos jelszókezelőben. Ezek felhasználhatók fiókodhoz való hozzáférés helyreállítására, ha a kétfaktoros hitelesítési eszközöd elveszne.',
                'show_codes' => 'Helyreállítási kódok megjelenítése',
                'hide_codes' => 'Helyreállítási kódok elrejtése',
            ],
            'confirmation' => [
                'success_notification' => 'A kód ellenőrizve. Kétfaktoros hitelesítés engedélyezve.',
                'invalid_code' => 'A megadott kód érvénytelen.',
            ],
        ],
        'sanctum' => [
            'title' => 'API Tokenek',
            'description' => 'Kezeld azokat az API-tokeneket, amelyek lehetővé teszik, hogy harmadik fél szolgáltatásai hozzáférjenek ehhez az alkalmazáshoz az te nevedben. MEGJEGYZÉS: a token csak egyszer jelenik meg a létrehozáskor. Ha elveszíted a tokent, törölnöd kell, és újat kell létrehoznod.',
            'create' => [
                'notify' => 'Az API token létrehozva.',
                'submit' => [
                    'label' => 'Létrehozás',
                ],
            ],
            'update' => [
                'notify' => 'Az API token frissítve.',
            ],
        ],
    ],
    'fields' => [
        'email' => 'E-mail cím',
        'login' => 'Bejelentkezés',
        'name' => 'Név',
        'password' => 'Jelszó',
        'password_confirm' => 'Jelszó megerősítése',
        'new_password' => 'Új jelszó',
        'new_password_confirmation' => 'Új jelszó megerősítése',
        'token_name' => 'Token neve',
        'abilities' => 'Képességek',
        '2fa_code' => 'Kód',
        '2fa_recovery_code' => 'Helyreállítási kód',
        'created' => 'Létrehozva',
        'expires' => 'Lejár',
    ],
    'or' => 'Vagy',
    'cancel' => 'Mégsem',
];
