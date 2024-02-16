<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Providers\RouteServiceProvider;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $navigationLabel = "Megtekintés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Impersonate::make()->label('Megszemélyesítés')->redirectTo(RouteServiceProvider::HOME),
        ];
    }
}
