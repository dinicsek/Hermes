<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TournamentMatchResource\Pages;
use App\Filament\Manager\Resources\TournamentMatchResource\RelationManagers;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\TournamentMatch;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TournamentMatchResource extends Resource
{
    protected static ?string $model = TournamentMatch::class;

    protected static ?string $navigationIcon = 'play-volleyball';

    protected static ?string $modelLabel = 'Meccs';
    protected static ?string $pluralLabel = 'Meccsek';

    protected static ?string $navigationGroup = 'Általános';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Verseny')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('round')
                    ->label('Forduló')
                    ->sortable(),
                Tables\Columns\TextColumn::make('homeTeam.name')
                    ->label('Hazai csapat')
                    ->color('info')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('home_team_score')
                    ->label('Hazai csapat pontszáma')
                    ->placeholder('Nincs megadva')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('awayTeam.name')
                    ->label('Vendég csapat')
                    ->color('danger')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('away_team_score')
                    ->label('Vendég csapat pontszáma')
                    ->placeholder('Nincs megadva')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('winner')
                    ->label('Győztes csapat')
                    ->placeholder('Nincs megadva')
                    ->state(fn(TournamentMatch $record) => match ($record->winner) {
                        TournamentMatchWinner::AWAY_TEAM => $record->awayTeam->name,
                        TournamentMatchWinner::HOME_TEAM => $record->homeTeam->name,
                        default => null,
                    })
                    ->badge()
                    ->color(fn(TournamentMatch $record) => match ($record->winner) {
                        TournamentMatchWinner::AWAY_TEAM => 'danger',
                        TournamentMatchWinner::HOME_TEAM => 'info',
                        default => null,
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_stakeless')
                    ->label('Tét nélküli')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Kezdés')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ended_at')
                    ->label('Befejezés')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('tournament_id')
                    ->label('Verseny')
                    ->relationship('tournament', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', auth()->id()))
                    ->preload()
                    ->searchable()
                    ->native(false),
            ])
            ->reorderable('sort')
            ->paginatedWhileReordering()
            ->actions([
                Tables\Actions\ViewAction::make()->label('Kezelés')->icon('heroicon-m-wrench-screwdriver')->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTournamentMatches::route('/'),
            'create' => Pages\CreateTournamentMatch::route('/create'),
            'view' => Pages\ViewTournamentMatch::route('/{record}'),
            'edit' => Pages\EditTournamentMatch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['homeTeam:id,name,tournament_id', 'awayTeam:id,name,tournament_id'])->orderBy('sort');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTournamentMatch::class,
            Pages\EditTournamentMatch::class,
        ]);
    }
}
