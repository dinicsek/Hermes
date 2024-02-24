<?php

namespace App\Filament\Manager\Resources\TeamResource\RelationManagers;

use App\Models\Enums\TournamentMatchWinner;
use App\Models\TournamentMatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';


    protected static ?string $title = 'Meccsek';

    protected static ?string $modelLabel = 'Meccs';

    protected static ?string $pluralLabel = 'Meccsek';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('round')
                    ->label('Forduló'),
                Tables\Columns\TextColumn::make('homeTeam.name')
                    ->label('Hazai csapat')
                    ->color('info')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('home_team_score')
                    ->label('Hazai csapat pontszáma')
                    ->placeholder('Nincs megadva')
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('awayTeam.name')
                    ->label('Vendég csapat')
                    ->color('danger')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('away_team_score')
                    ->label('Vendég csapat pontszáma')
                    ->placeholder('Nincs megadva')
                    ->weight(FontWeight::Bold),
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
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_stakeless')
                    ->label('Tét nélküli')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Kezdés')
                    ->dateTime()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ended_at')
                    ->label('Befejezés')
                    ->dateTime()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Módosítva')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Törölve')
                    ->placeholder('Nincs törölve')
                    ->dateTime()
                    ->since()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->ordered()->with(['homeTeam', 'awayTeam']);
            });
    }
}
