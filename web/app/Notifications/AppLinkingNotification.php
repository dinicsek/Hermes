<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AppLinkingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $teamName,
        public string $tournamentName,
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
            ->subject('Csapatod jóvá lett hagyva')
            ->greeting('Gratulálunk!')
            ->line(new HtmlString('A csapatod (' . e($this->teamName) . ') jelentkezése jóvá lett hagyva a <b>' . e($this->tournamentName) . '</b> versenyen való részvételhez.'))
            ->line(new HtmlString("Amennyiben szeretnél értesítéseket kapni, mielőtt a te csapatod meccse következik, kérlek telepítsd az <a href='{$appDownloadUrl}'>alkalmazásunkat</a>, és utána kattints az alábbi gombra.")) //TODO: add link
            ->action('Alkalmazás összekapcsolása', route('app-linking', ['token' => $this->appLinkingToken]))
            ->line('Amennyiben nem tudod megnyitni a fentebbi gomb segítségével az alkalmazást, kérlek másold be a következő linket a böngésződ címsorába és válaszd a megnyitás alkalmazással opciót:')
            ->line('hermes://app-linking/' . $this->appLinkingToken)
            ->line('Sok sikert kívánunk a versenyhez!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
