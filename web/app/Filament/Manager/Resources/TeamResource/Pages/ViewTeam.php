<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Manager\Resources\TeamResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected static ?string $navigationLabel = "Megtekintés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_notification')->label('Értesítés küldése')->color('gray')->icon('heroicon-o-paper-airplane')->form([
                TextInput::make('title')->label('Cím')->required(),
                TextInput::make('body')->label('Tartalom')->required(),
            ])->action(function (ViewTeam $livewire, array $data) {
                $tokens = $livewire->getRecord()->push_tokens;

                if ($tokens === []) {
                    Notification::make()->title('Nem lett kiküldve értesítés, mivel nincsenek összekapcsolt eszközök.')->danger()->send();
                    return;
                }

                $messaging = app('firebase.messaging');

                $message = CloudMessage::new()->withNotification([
                    'title' => $data['title'],
                    'body' => $data['body'],
                ])->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                ]));

                $messaging->sendMulticast($message, $tokens);
            }),
            Actions\DeleteAction::make(),
        ];
    }
}
