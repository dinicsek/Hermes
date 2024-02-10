<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TeamResource\Pages;
use App\Filament\Manager\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use App\Models\Tournament;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Csapat';
    protected static ?string $pluralLabel = 'Csapatok';

    protected static ?string $navigationGroup = 'Általános';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Csapatnév')
                    ->maxLength(255)
                    ->required(),
                Select::make('tournament_id')
                    ->label('Verseny')
                    ->helperText(fn(string $operation) => $operation === 'edit' ? 'Figyelem: új verseny megadásakor az összes csatolt csoport megszűnik csatolva lenni!' : null)
                    ->relationship('tournament', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', auth()->id()))
                    ->searchable(['name'])
                    ->preload()
                    ->required()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set, int $state) {
                        if ($state == null)
                            return;

                        $tournament = Tournament::find($state, ['id', 'min_team_size', 'max_team_size', 'max_approved_teams']);
                        $approvedTeams = $tournament->teams()->whereIsApproved(true)->count();

                        $set('min_members', $tournament->min_team_size); // Weird workaround, but it works
                        $set('max_members', $tournament->max_team_size);
                        $set('max_approved_teams', $tournament->max_approved_teams);
                        $set('approved_teams', $approvedTeams);

                        if ($tournament->max_approved_teams !== null && $tournament->max_approved_teams <= $approvedTeams)
                            $set('is_approved', false);
                    }),
                Toggle::make('is_approved')
                    ->label('Jóváhagyva')
                    ->helperText(fn(Get $get) => $get('approved_teams') === null || ($get('max_approved_teams') === null && $get('max_approved_teams') <= $get('approved_teams')) ? 'Jóváhagyás után a megadott e-mailekre ki lesz küldve az összekapcsolási kérelem.' : 'Ez a verseny már elérte a maximális jóváhagyott csapatszámot (' . $get('max_approved_teams') . ' csapat).')
                    ->default(true)
                    ->afterStateHydrated(function (Set $set, Get $get, string $operation) {
                        if ($operation !== 'edit')
                            return;

                        $tournament = Tournament::find($get('tournament_id'), ['id', 'min_team_size', 'max_team_size', 'max_approved_teams']);
                        $approvedTeams = $tournament->teams()->whereIsApproved(true)->count();

                        $set('min_members', $tournament->min_team_size); // Weird workaround, but it works
                        $set('max_members', $tournament->max_team_size);
                        $set('max_approved_teams', $tournament->max_approved_teams);
                        $set('approved_teams', $approvedTeams);
                    })
                    ->disabled(fn(Get $get, string $operation, ?Team $record) => (($record !== null && $operation !== 'create') && $record->is_approved === false || $operation !== 'edit') && $get('max_approved_teams') !== null && $get('max_approved_teams') <= $get('approved_teams')),
                \Filament\Forms\Components\Section::make('Csapattagok')
                    ->schema([
                        TagsInput::make('members')
                            ->label('Csapattagok')
                            ->helperText(fn(Get $get) => sprintf('Minimum %d, maximum %d csapattag adható ehhez a csapathoz.', $get('min_members'), $get('max_members')))
                            ->placeholder('Csapattagok hozzáadása')
                            ->required()
                            ->rules([fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                if (count($value) < $get('min_members') || count($value) > $get('max_members'))
                                    $fail('A csapatagok száma nem felel meg a verseny által meghatározott minimum és maximum értéknek.');

                            }]),
                        TagsInput::make('emails')
                            ->label('Értesítendő e-mail címek')
                            ->helperText(fn(Get $get) => sprintf('Maximum %d e-mail cím adható ehhez a csapathoz.', $get('max_members')))
                            ->placeholder('E-mail címek hozzáadása')
                            ->rules([fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                if (count($value) > $get('max_members'))
                                    $fail('Az értesítendő e-mail címek száma nem felel meg a verseny által meghatározott maximum értéknek.');
                            }])
                            ->nestedRecursiveRules([
                                'email'
                            ]),
                    ])->columns()->visible(fn(Get $get) => $get('tournament_id') !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Csapatnév')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Verseny')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('members')
                    ->label('Tagok')
                    ->words(6)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Jóváhagyva')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('tournament_id')
                    ->label('Verseny')
                    ->relationship('tournament', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', auth()->id()))
                    ->preload()
                    ->searchable()
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Jóváhagyva')
            ])
            ->groups([
                Tables\Grouping\Group::make('tournament.name')
                    ->label('Verseny')
                    ->collapsible(),
                Tables\Grouping\Group::make('is_approved')
                    ->label('Jóváhagyva')
                    ->getTitleFromRecordUsing(fn(Team $record) => $record->is_approved ? 'Jóváhagyva' : 'Nincs jóváhagyva')
                    ->collapsible(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Split::make([
                Grid::make(1)->schema([
                    Section::make([
                        TextEntry::make('name')
                            ->label('Név'),
                        TextEntry::make('tournament.name')
                            ->label('Verseny'),

                        IconEntry::make('is_approved')
                            ->label('Jóváhagyva')
                            ->boolean(),
                    ])->columns()->grow(),
                    Section::make('Csapattagok')
                        ->schema([
                            TextEntry::make('members')
                                ->label('Csapattagok'),
                            TextEntry::make('emails')
                                ->label('E-mail címek')
                                ->placeholder("Nincsenek e-mail címek megadva"),
                        ])->grow(),
                ])->grow(),
                Section::make([
                    TextEntry::make('created_at')
                        ->label('Létrehozva')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->label('Módosítva')
                        ->dateTime()
                ])->grow(false),
            ])
        ])->columns(false);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GroupsRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'view' => Pages\ViewTeam::route('/{record}'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereHas('tournament', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTeam::class,
            Pages\EditTeam::class,
        ]);
    }
}
