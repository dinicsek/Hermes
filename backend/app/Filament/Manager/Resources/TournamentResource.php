<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TournamentResource\Pages;
use App\Filament\Manager\Resources\TournamentResource\RelationManagers;
use App\Models\Tournament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $modelLabel = 'Verseny';

    protected static ?string $pluralLabel = 'Versenyek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(2),
                DateTimePicker::make('registration_starts_at')
                    ->label('Regisztráció kezdete')
                    ->required()
                    ->before('registration_ends_at')
                    ->native(false),
                DateTimePicker::make('registration_ends_at')
                    ->label('Regisztráció vége')
                    ->required()
                    ->after('registration_starts_at')
                    ->native(false),
                DateTimePicker::make('starts_at')
                    ->label('Kezdés')
                    ->required()
                    ->before(fn(Get $get) => $get('ended_at') === null ? null : 'ended_at')
                    ->native(false),
                DateTimePicker::make('ended_at')
                    ->label('Lezárult')
                    ->placeholder('Nem zárult le')
                    ->hiddenOn('create')
                    ->after('starts_at')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kód')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Állapot')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_starts_at')
                    ->label('Regisztráció kezdete')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_ends_at')
                    ->label('Regisztráció vége')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Módosítva')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Törölve')
                    ->placeholder('Nincs törölve')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Kezelés')->icon('heroicon-m-wrench-screwdriver')->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'view' => Pages\ViewTournament::route('/{record}'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('user_id', auth()->id());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTournament::class,
            Pages\EditTournament::class,
        ]);
    }
}
