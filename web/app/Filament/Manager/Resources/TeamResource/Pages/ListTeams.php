<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Exports\TeamExporter;
use App\Filament\Manager\Resources\TeamResource;
use App\Models\Team;
use App\Models\Tournament;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_notification')->label('Értesítés küldése')->color('gray')->icon('heroicon-o-paper-airplane')->form([
                Select::make('tournament_id')->label('Verseny')->options(fn() => Tournament::pluck('name', 'id')->toArray())->required(),
                TextInput::make('title')->helperText('Ez jelenik csak meg, amikor az értesítés le van csukva.')->label('Az értesítés címe')->required(),
                Textarea::make('body')->label('Az értesítés tartalma')->required(),
                ColorPicker::make('color')->label('Értesítés színe')->default('#3b82f6'),
            ])->modalSubmitAction(fn(Actions\StaticAction $action) => $action->label('Kiküldés'))->action(function (array $data) {
                $tokens = Team::where('tournament_id', $data['tournament_id'])->pluck('push_tokens')->flatten()->toArray();

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
            ExportAction::make('export')
                ->label('Exportálás')
                ->tooltip('Elérhető formátumok: CSV, XLSX. Az exportálás figyelembe veszi a jelenlegi szűrőket.')
                ->exporter(TeamExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
