<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Manager\Resources\TeamResource;
use App\Helpers\AppLinking\AppLinkingHelper;
use App\Notifications\AppLinkingNotification;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TagsInput;
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
            Actions\Action::make('send_app_linking_notification')->label('Összekapcsolási értesítés küldése')->color('gray')->form([
                Toggle::make('include_team_emails')->label('Csapat tagok értesítése')->default(true),
                TagsInput::make('emails')
                    ->label('E-mail címek')
                    ->nestedRecursiveRules([
                        'email'
                    ]),
            ])->action(function (ViewTeam $livewire, array $data) {
                $team = $livewire->getRecord();

                $appLinkingHelper = app(AppLinkingHelper::class);

                if ($data['include_team_emails'])
                    $appLinkingHelper->sendAppLinkingNotifications($team);

                if ($data['emails'] !== null) {
                    foreach ($data['emails'] as $email) {
                        $token = $appLinkingHelper->generateAppLinkingToken($team->id, $email);
                        \Illuminate\Support\Facades\Notification::route('mail', $email)
                            ->notify(new AppLinkingNotification($team->name, $team->tournament->name, $token));
                    }
                }

                Notification::make()->title('Az értesítések sikeresen kiküldve.')->success()->send();
            }),
            Actions\Action::make('send_notification')->label('Értesítés küldése')->color('gray')->icon('heroicon-o-paper-airplane')->form([
                TextInput::make('title')->helperText('Ez jelenik csak meg, amikor az értesítés le van csukva.')->label('Az értesítés címe')->required(),
                Textarea::make('body')->label('Az értesítés tartalma')->required(),
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
                    ],
                ]);

                $message = CloudMessage::new()->withNotification([
                    'title' => $data['title'],
                    'body' => $data['body'],
                ])->withAndroidConfig($androidConfig);

                $messaging->sendMulticast($message, $tokens);

                Notification::make()->title('Értesítés sikeresen kiküldve.')->success()->send();
            }),
            Actions\DeleteAction::make(),
        ];
    }
}
