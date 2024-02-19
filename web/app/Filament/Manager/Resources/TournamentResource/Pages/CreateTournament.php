<?php

namespace App\Filament\Manager\Resources\TournamentResource\Pages;

use App\Filament\Manager\Resources\TournamentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTournament extends CreateRecord
{
    protected static string $resource = TournamentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = Str::random(6);
        $data['user_id'] = auth()->id();

        return $data;
    }
}
