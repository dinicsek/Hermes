<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Manager\Resources\TeamResource;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                TextInput::make('title')->helperText('Ez jelenik csak meg, amikor az értesítés le van csukva.')->label('Az értesítés címe')->required(),
                Textarea::make('body')->label('Az értesítés tartalma')->required(),
                Toggle::make('notify')->label('Rezgés és hangjelzés küldése')->default(true),
                ColorPicker::make('color')->label('Értesítés színe')->default('#3b82f6'),
            ])->modalSubmitAction(fn(Actions\StaticAction $action) => $action->label('Kiküldés'))->action(function (ViewTeam $livewire, array $data) {
                $tokens = $livewire->getRecord()->push_tokens;

                if ($tokens === []) {
                    Notification::make()->title('Nem lett kiküldve értesítés, mivel nincsenek összekapcsolt eszközök.')->danger()->send();
                    return;
                }

                $messaging = app('firebase.messaging');

                $androidConfig = AndroidConfig::fromArray([
                    'priority' => 'high',
                    'notification' => [
                        'color' => $data['color'],
                        'sound' => 'default',
                        'vibrate_timings' => ['0.5s', '1s', '0.5s', '1s', '0.5s'],
                    ],
                ]);

                if ($data['notify'] === false) {
                    $androidConfig = AndroidConfig::fromArray([
                        'priority' => 'high',
                        'notification' => [
                            'color' => $data['color'],
                        ],
                    ]);
                }

                $message = CloudMessage::new()->withNotification([
                    'title' => $data['title'],
                    'body' => $data['body'],
                ])->withAndroidConfig($androidConfig);

                $messaging->sendMulticast($message, $tokens);
            }),
            Actions\DeleteAction::make(),
        ];
    }
}
