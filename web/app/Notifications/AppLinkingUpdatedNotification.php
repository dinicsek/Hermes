<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AppLinkingUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $appLinkingToken
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $appDownloadUrl = route('mobile-app-download');
        return (new MailMessage)
            ->subject('Alkalmazás összekapcsolása')
            ->greeting('Helló!')
            ->line(new HtmlString("Nemrégiben megváltoztattunk pár dolgot az alkalmazás összekapcsolásával kapcsolatban, és ha eddig nem tudtad letölteni, vagy összekapcsolni az alkalmazást, akkor most már valószínűleg sikerülni fog. Ha már ezelőtt sikerült, akkor most nem kell tenned semmit! <a href='{$appDownloadUrl}'>Alkalmazás letöltése (Android)</a>"))
            ->line('Miért is jó ezt megtenni?')
            ->line('Mert így soha nem kell majd feltenned azt a kérdést, hogy "Mikor jövünk mi?", mert mielőtt a te csapatod meccse következik, mi pont időben értesítünk, hogy mikor kezdj el készülni vagy esetleg átöltözni, és azt is megmondjuk, hogy melyik térfélen, ki ellen fogtok játszani.')
            ->action('Alkalmazás összekapcsolása', route('app-linking', ['token' => $this->appLinkingToken]))
            ->line('Amennyiben nem tudod továbbra sem megnyitni a fentebbi gomb segítségével az alkalmazást, kérlek másold be a következő linket a böngésződ címsorába és válaszd a megnyitás alkalmazással opciót:')
            ->line('hermes://app-linking/' . $this->appLinkingToken)
            ->line('Sok sikert kívánunk a versenyzéhez!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
