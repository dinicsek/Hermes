<?php

namespace App\Filament\Exports;

use App\Models\Enums\TournamentMatchWinner;
use App\Models\Team;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TeamExporter extends Exporter
{
    protected static ?string $model = Team::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Csapatnév'),
            ExportColumn::make('members')
                ->label('Tagok'),
            ExportColumn::make('matches_count')
                ->label('Meccsek száma')
                ->state(fn(Team $record) => $record->matches()->count()),
            ExportColumn::make('wins_count')
                ->label('Győzelmek száma')
                ->state(function (Team $record) {
                    $homeWins = $record->homeMatches()->where('winner', TournamentMatchWinner::HOME_TEAM)->count();
                    $awayWins = $record->awayMatches()->where('winner', TournamentMatchWinner::AWAY_TEAM)->count();

                    return $homeWins + $awayWins;
                }),
            ExportColumn::make('total_score')
                ->label('Összes szerzett pont')
                ->state(function (Team $record) {
                    $homeScore = $record->homeMatches()->sum('home_team_score');
                    $awayScore = $record->awayMatches()->sum('away_team_score');

                    return $homeScore + $awayScore;
                }),
            ExportColumn::make('total_conceded')
                ->label('Összes ellenük szerzett pont')
                ->state(function (Team $record) {
                    $homeConceded = $record->homeMatches()->sum('away_team_score');
                    $awayConceded = $record->awayMatches()->sum('home_team_score');

                    return $homeConceded + $awayConceded;
                }),
            ExportColumn::make('average_score')
                ->label('Átlagosan szerzett pont')
                ->state(function (Team $record) {
                    $homeScore = $record->homeMatches()->sum('home_team_score');
                    $awayScore = $record->awayMatches()->sum('away_team_score');

                    $score = $homeScore + $awayScore;

                    $matchCount = $record->homeMatches()->whereNotNull('home_team_score')->count() + $record->awayMatches()->whereNotNull('away_team_score')->count();

                    if ($matchCount === 0)
                        return 0;

                    return $score / $matchCount;
                }),
            ExportColumn::make('average_conceded')
                ->label('Átlagosan ellenük szerzett pont')
                ->state(function (Team $record) {
                    $homeConceded = $record->homeMatches()->sum('away_team_score');
                    $awayConceded = $record->awayMatches()->sum('home_team_score');

                    $conceded = $homeConceded + $awayConceded;

                    $matchCount = $record->homeMatches()->whereNotNull('away_team_score')->count() + $record->awayMatches()->whereNotNull('home_team_score')->count();

                    if ($matchCount === 0)
                        return 0;

                    return $conceded / $matchCount;
                }),
            ExportColumn::make('created_at')
                ->label('Létrehozás dátuma')
                ->formatStateUsing(fn($state) => $state->format('Y-m-d H:i:s')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A csapatok exportálása sikeres! ' . number_format($export->successful_rows) . ' sor került exportálásra.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' sor exportálása sikertelen volt.';
        }

        return $body;
    }
}
