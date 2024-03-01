<?php

namespace App\Livewire;

use App\Models\Enums\TournamentMatchWinner;
use App\Models\Team;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class OngoingTeamsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public int $tournamentId;

    public function table(Table $table): Table
    {
        return $table
            ->query(Team::query())
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->badge(),
                    TextColumn::make('members'),
                    TextColumn::make('wins_and_losses')
                        ->state(function (Team $record) {
                            $homeWins = $record->homeMatches->where('winner', TournamentMatchWinner::HOME_TEAM)->count();
                            $awayWins = $record->awayMatches->where('winner', TournamentMatchWinner::AWAY_TEAM)->count();

                            $totalWins = $homeWins + $awayWins;

                            $homeLosses = $record->homeMatches->where('winner', '!=', TournamentMatchWinner::HOME_TEAM)->count();
                            $awayLosses = $record->awayMatches->where('winner', '!=', TournamentMatchWinner::AWAY_TEAM)->count();

                            $totalLosses = $homeLosses + $awayLosses;

                            return new HtmlString('(W) <strong>' . $totalWins . '</strong> / ' . '(L) <strong>' . $totalLosses . '</strong>');
                        }),
                    TextColumn::make('score')
                        ->state(function (Team $record) {
                            $homeScores = $record->homeMatches->sum('home_team_score');
                            $awayScores = $record->awayMatches->sum('away_team_score');

                            $totalScores = $homeScores + $awayScores;

                            return new HtmlString('Szerzett pontok: <strong>' . $totalScores . '</strong>');
                        }),
                ])->space(2)
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->filters([
                //
            ])->modifyQueryUsing(function ($query) {
                return $query->with(['homeMatches', 'awayMatches'])->where('tournament_id', $this->tournamentId);
            });
    }

    public function render()
    {
        return view('livewire.ongoing-teams-table');
    }
}
