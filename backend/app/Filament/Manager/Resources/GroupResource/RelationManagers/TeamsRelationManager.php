<?php

namespace App\Filament\Manager\Resources\GroupResource\RelationManagers;

use App\Models\Team;
use Closure;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $modelLabel = 'Csapat';

    protected static ?string $pluralLabel = 'Csapatok';

    protected static ?string $title = 'Csapatok';

    public function form(Form $form): Form
    {
        $approvedTeams = $this->ownerRecord->tournament->teams()->where('is_approved', true)->count();

        $minTeamSize = $this->ownerRecord->tournament->min_team_size;
        $maxTeamSize = $this->ownerRecord->tournament->max_team_size;

        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Csapatnév')
                    ->maxLength(255)
                    ->required(),
                Toggle::make('is_approved')
                    ->label('Jóváhagyva')
                    ->helperText(fn(Get $get) => ($this->ownerRecord->tournament->max_approved_teams === null || $this->ownerRecord->tournament->max_approved_teams >= $approvedTeams) ? 'Jóváhagyás után a megadott e-mailekre ki lesz küldve az összekapcsolási kérelem.' : 'Ez a verseny már elérte a maximális jóváhagyott csapatszámot (' . $this->ownerRecord->tournament->max_approved_teams . ' csapat).')
                    ->default(true)
                    ->afterStateHydrated(function (string $operation, Set $set) use ($approvedTeams) {
                        if ($operation === 'edit')
                            return;

                        if ($this->ownerRecord->tournament->max_approved_teams !== null && $this->ownerRecord->tournament->max_approved_teams <= $approvedTeams)
                            $set('is_approved', false);
                    })
                    ->disabled(fn(Get $get, ?Team $record, string $operation) => (($record !== null && $operation !== 'create') && $record->is_approved === false || $operation !== 'edit') && $this->ownerRecord->tournament->max_approved_teams !== null && $this->ownerRecord->tournament->max_approved_teams <= $approvedTeams),
                Section::make('Csapattagok')
                    ->schema([
                        TagsInput::make('members')
                            ->label('Csapattagok')
                            ->helperText(fn() => sprintf('Minimum %d, maximum %d csapattag adható ehhez a csapathoz.', $minTeamSize, $maxTeamSize))
                            ->placeholder('Csapattagok hozzáadása')
                            ->required()
                            ->rules([function () use ($minTeamSize, $maxTeamSize) {
                                return function (string $attribute, $value, Closure $fail) use ($minTeamSize, $maxTeamSize) {
                                    if (count($value) < $minTeamSize || count($value) > $maxTeamSize)
                                        $fail('A csapatagok száma nem felel meg a verseny által meghatározott minimum és maximum értéknek.');
                                };
                            }]),
                        TagsInput::make('emails')
                            ->label('Értesítendő e-mail címek')
                            ->helperText(fn(Get $get) => sprintf('Maximum %d e-mail cím adható ehhez a csapathoz.', $maxTeamSize))
                            ->placeholder('E-mail címek hozzáadása')
                            ->rules([function () use ($maxTeamSize) {
                                return function (string $attribute, $value, Closure $fail) use ($maxTeamSize) {
                                    if (count($value) > $maxTeamSize)
                                        $fail('Az értesítendő e-mail címek száma nem felel meg a verseny által meghatározott maximum értéknek.');
                                };
                            }])
                            ->nestedRecursiveRules([
                                'email'
                            ]),
                    ])->columns(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Csapatnév')
                    ->sortable(),
                Tables\Columns\TextColumn::make('members')
                    ->label('Csapattagok')
                    ->words(5)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(function (array $data, string $model) {
                    $data['tournament_id'] = $this->ownerRecord->tournament_id;
                    return $this->ownerRecord->teams()->create($data);
                }),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->where('tournament_id', $this->ownerRecord->tournament_id)->select('teams.id', 'teams.name', 'teams.tournament_id')),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    #[On('refresh_teams')]
    public function refresh()
    {

    }
}
