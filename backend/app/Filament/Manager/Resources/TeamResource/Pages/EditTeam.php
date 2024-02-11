<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Events\TeamApprovedEvent;
use App\Filament\Manager\Resources\TeamResource;
use Facades\App\Helpers\AppLinking\AppLinking;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected static ?string $navigationLabel = "Szerkesztés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Action::make('resend_app_linking_notifications')
                ->label('Jóváhagyási értesítések újraküldése')
                ->color('gray')
                ->disabled(!$this->record->is_approved)
                ->action(function () {
                    AppLinking::sendAppLinkingNotifications($this->record);

                    Notification::make()
                        ->success()
                        ->title('Sikeres újraküldés')
                        ->send();
                })
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tournament = $this->record->tournament;

        $data['min_members'] = $tournament->min_team_size;
        $data['max_members'] = $tournament->max_team_size;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['min_members']);
        unset($data['max_members']);
        unset($data['max_approved_teams']);
        unset($data['approved_teams']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged(['is_approved']) && $this->record->is_approved) {
            TeamApprovedEvent::dispatch($this->record);
        }

        if ($this->record->wasChanged(['tournament_id'])) {
            $this->record->groups()->detach();
            $this->dispatch('refresh_groups');
        }
    }
}
