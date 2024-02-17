<?php

namespace App\Livewire;

use App\Models\Enums\EventStatus;
use App\Models\Tournament;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class Tournaments extends SimplePage implements HasForms
{
    use InteractsWithFormActions;

    protected static string $view = 'livewire.tournaments';

    protected static ?string $title = 'Versenyek';
    public ?array $data = [];
    protected ?string $subheading = 'Regisztrálj egy versenyre vagy kövess nyomon egyet!';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Verseny kódja')
                    ->placeholder('Például: a1b2c3')
                    ->mask('******')
                    ->validationMessages([
                        'exists' => 'Nincs ilyen kódú verseny. Kérlek, ellenőrizd, hogy nem írtad-e el a kódot!',
                    ])
                    ->exists('tournaments', 'code')
                    ->required(),
                // ...
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('submit')->label('Gyerünk!')->submit('redirectToTournament')
        ];
    }

    public function redirectToTournament(): RedirectResponse|Redirector
    {
        $this->form->validate();

        $tournament = Tournament::whereCode($this->data['code'])->firstOrFail();

        if ($tournament->status === EventStatus::UPCOMING && $tournament->registration_starts_at->isPast() && $tournament->registration_ends_at->isFuture()) {
            return redirect()->route('register-for-tournament', $tournament);
        } elseif ($tournament->status === EventStatus::UPCOMING) {
            return redirect()->route('upcoming-tournament', $tournament);
        }

        return redirect('/');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
