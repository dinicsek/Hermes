<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Exports\TeamExporter;
use App\Filament\Manager\Resources\TeamResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export')
                ->label('Exportálás')
                ->exporter(TeamExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
