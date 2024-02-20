<?php

namespace App\Livewire;

use App\Data\TournamentData;
use App\Events\TeamApprovedEvent;
use App\Models\Enums\EventStatus;
use App\Models\Tournament;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class RegisterForTournamentPage extends SimplePage implements HasForms
{
    use InteractsWithFormActions;

    protected static string $view = 'livewire.register-for-tournament-page';

    public TournamentData $tournamentData;
    public int $tournamentId;
    public bool $isTournamentFull = false;
    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return $this->tournamentData->name . ' - Regisztráció';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Regisztrálni egészen ' . $this->tournamentData->registration_ends_at->format('Y. m. d. H:i') . '-ig lehetséges, míg a verseny ' . $this->tournamentData->starts_at->format('Y. m. d. H:i') . '-kor kezdődik.';
    }

    public function mount(Tournament $tournament): void
    {
        if ($tournament->status === EventStatus::UPCOMING && ($tournament->registration_starts_at->isFuture() || $tournament->registration_ends_at->isPast())) {
            redirect()->route('upcoming-tournament', $tournament);
        }

        $this->tournamentId = $tournament->id;
        $this->tournamentData = TournamentData::from($tournament);

        $this->isTournamentFull = $tournament->max_approved_teams !== null && $tournament->max_approved_teams <= $tournament->teams()->whereIsApproved(true)->count();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $tournamentData = $this->tournamentData;
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Csapatnév')
                    ->required(),
                TagsInput::make('members')
                    ->label('Csapattagok')
                    ->helperText(fn(Get $get) => sprintf('Minimum %d, maximum %d csapattag adható meg.', $tournamentData->min_team_size, $tournamentData->max_team_size))
                    ->placeholder('Csapattagok hozzáadása')
                    ->required()
                    ->rules([fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($tournamentData) {
                        if (count($value) < $tournamentData->min_team_size || count($value) > $tournamentData->max_team_size)
                            $fail('A csapatagok száma nem felel meg a verseny által meghatározott minimum és maximum értéknek.');

                    }]),
                TagsInput::make('emails')
                    ->label('Értesítendő e-mail címek')
                    ->helperText(fn(Get $get) => sprintf('Maximum %d e-mail cím adható meg.', $tournamentData->max_team_size))
                    ->hintIcon('heroicon-m-information-circle')
                    ->hintIconTooltip('Amennyiben, megadsz e-mail címeket, értesíteni fogunk amikor elfogadták a redisztrációd, valamint lehetőséged lesz telefonos értesítéseket kapni a meccseid előtt.')
                    ->placeholder('E-mail címek hozzáadása')
                    ->rules([fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($tournamentData) {
                        if (count($value) > $tournamentData->max_team_size)
                            $fail('Az értesítendő e-mail címek száma nem felel meg a verseny által meghatározott maximum értéknek.');
                    }])
                    ->nestedRecursiveRules([
                        'email'
                    ])
            ])
            ->statePath('data')
            ->disabled($this->isTournamentFull);
    }

    public function getFormActions(): array
    {
        return [
            Action::make('submit')->label('Regisztrálok!')->submit('submitRegistration')
        ];
    }

    public function submitRegistration(): void
    {
        $this->form->validate();

        $tournament = Tournament::findOrFail($this->tournamentId);

        $team = $tournament->teams()->create([
            'name' => $this->data['name'],
            'members' => $this->data['members'],
            'emails' => $this->data['emails'] ?? [],
            'is_approved' => $tournament->approve_by_default,
        ]);

        if ($team->is_approved) {
            TeamApprovedEvent::dispatch($team);
        }

        Notification::make('registration-submitted')->success()->title('Sikeres regisztráció!')->send();
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
