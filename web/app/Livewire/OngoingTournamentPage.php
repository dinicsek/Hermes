<?php

namespace App\Livewire;

use App\Data\TournamentData;
use App\Data\TournamentMatchData;
use App\Models\Enums\EventStatus;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Livewire\Component;

class OngoingTournamentPage extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    #[Url]
    public string $activeTab = 'matches';
    public int $tournamentId;
    public string $tournamentCode;
    public ?TournamentMatchData $currentTournamentMatchData = null;
    public TournamentData $tournamentData;

    public function mount(Tournament $tournament): void
    {
        if ($tournament->status === EventStatus::UPCOMING && ($tournament->registration_starts_at->isFuture() || $tournament->registration_ends_at->isPast())) {
            redirect()->route('upcoming-tournament', $tournament);
        } elseif ($tournament->status === EventStatus::UPCOMING && $tournament->registration_starts_at->isPast() && $tournament->registration_ends_at->isFuture()) {
            redirect()->route('register-for-tournament', $tournament);
        }

        $this->tournamentId = $tournament->id;
        $this->tournamentData = TournamentData::from($tournament);

        $this->tournamentCode = $tournament->code;
        $currentMatchArray = Cache::get('tournament.' . $tournament->code . '.current-match');

        if ($currentMatchArray !== null) {
            $this->currentTournamentMatchData = TournamentMatchData::from($currentMatchArray);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TournamentMatch::query()->where('tournament_id', $this->tournamentId))
            ->columns([
                Stack::make([
                    TextColumn::make('round')
                        ->weight(FontWeight::Bold)
                        ->formatStateUsing(fn(string $state, TournamentMatch $record) => $state . '. forduló' . ($record->is_final ? ' - Tét nélküli' : '') . ($record->is_final ? ' - Döntő' : '')),
                    TextColumn::make('home_team')
                        ->state(function (TournamentMatch $record) {
                            return new HtmlString($record->homeTeam->name . ($record->home_team_score !== null ? ' - <strong>' . $record->home_team_score . '</strong>' : ''));
                        })
                        ->badge()
                        ->color('info'),
                    TextColumn::make('away_team')
                        ->state(function (TournamentMatch $record) {
                            return new HtmlString($record->awayTeam->name . ($record->away_team_score !== null ? ' - <strong>' . $record->away_team_score . '</strong>' : ''));
                        })
                        ->badge()
                        ->color('danger'),
                    TextColumn::make('time')
                        ->label('Kezdés - Befejezés')
                        ->state(function (TournamentMatch $record) {
                            if ($record->started_at === null)
                                return new HtmlString('<span class="text-gray-500 dark:text-gray-400">Még nem kezdődött el</span>');
                            return $record->started_at->format('Y. m. d. H:i:s') . ' - ' . ($record->ended_at === null ? '...' : $record->ended_at->format('H:i:s'));
                        })
                ])->space(2)
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->filters([
                TernaryFilter::make('upcoming_or_ongoing')
                    ->label('Jövőbeli')
                    ->queries(
                        true: fn(Builder $query) => $query->whereEndedAt(null)->whereStartedAt(null),
                        false: fn(Builder $query) => $query->whereNotNull('ended_at'),
                        blank: fn(Builder $query) => $query,
                    ),
                SelectFilter::make('teams')
                    ->label('Csapatok')
                    ->options(Team::where('tournament_id', $this->tournamentId)->pluck('name', 'id')->toArray())
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['values']))
                            return $query;

                        return $query->whereHas('homeTeam', function ($query) use ($data) {
                            $query->whereIn('id', $data['values']);
                        })->orWhereHas('awayTeam', function ($query) use ($data) {
                            $query->whereIn('id', $data['values']);
                        });
                    })
            ], layout: FiltersLayout::Modal)
            ->modifyQueryUsing(function ($query) {
                return $query->ordered()->whereNotNull(['home_team_id', 'away_team_id'])->with(['homeTeam', 'awayTeam']);
            });
    }

    public function render()
    {
        return view('livewire.ongoing-tournament-page');
    }

    public function updateCurrentTournamentMatchData($data)
    {
        $this->currentTournamentMatchData = TournamentMatchData::from($data);
        $this->dispatch('match-changed');
    }

    public function refreshCurrentTournamentMatchData()
    {
        $cachedData = Cache::get('tournament.' . $this->tournamentCode . '.current-match');

        if ($cachedData === null) {
            $this->currentTournamentMatchData = null;
            return;
        }

        $this->currentTournamentMatchData = TournamentMatchData::from($cachedData);
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }
}
