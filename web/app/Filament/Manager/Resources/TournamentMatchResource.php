<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TournamentMatchResource\Pages;
use App\Filament\Manager\Resources\TournamentMatchResource\RelationManagers;
use App\Filament\Manager\Resources\TournamentMatchResource\Widgets\ManageTournamentMatch;
use App\Models\Enums\RoundMode;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\TournamentMatch;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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
                Select::make('tournament_id')
                    ->label('Verseny')
                    ->relationship('tournament', 'name', modifyQueryUsing: fn($query) => $query->where('user_id', auth()->id()))
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live(),
                TextInput::make('round')
                    ->label('Forduló')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Section::make('Csapatok')->schema([
                    Select::make('home_team_id')
                        ->label('Hazai csapat')
                        ->relationship('homeTeam', 'name', modifyQueryUsing: fn($query, Get $get) => $query->where('tournament_id', $get('tournament_id'))->where("is_approved", true))
                        ->required()
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->notIn(fn(Get $get) => [$get('away_team_id')])
                        ->validationMessages([
                            'not_in' => 'A hazai és a vendég csapat nem lehet ugyanaz.',
                        ])
                        ->selectablePlaceholder(false),
                    TextInput::make('home_team_score')
                        ->label('Hazai csapat pontszáma')
                        ->helperText('Ha ez a meccs még nem kezdődött el, akkor hagyd üresen.')
                        ->numeric()
                        ->minValue(0),
                    Select::make('away_team_id')
                        ->label('Vendég csapat')
                        ->relationship('awayTeam', 'name', modifyQueryUsing: fn($query, Get $get) => $query->where('tournament_id', $get('tournament_id'))->where("is_approved", true))
                        ->required()
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->notIn(fn(Get $get) => [$get('home_team_id')])
                        ->validationMessages([
                            'not_in' => 'A hazai és a vendég csapat nem lehet ugyanaz!',
                        ])
                        ->selectablePlaceholder(false),
                    TextInput::make('away_team_score')
                        ->label('Vendég csapat pontszáma')
                        ->helperText('Ha ez a meccs még nem kezdődött el, akkor hagyd üresen!')
                        ->numeric()
                        ->minValue(0),
                    Select::make('winner')
                        ->label('Győztes csapat')
                        ->options(TournamentMatchWinner::class)
                        ->native(false),
                ])->columns()->visible(fn(Get $get) => $get('tournament_id') !== null),
                Section::make('Beállítások és időpontok')->schema([
                    Toggle::make('is_stakeless')
                        ->label('Tét nélküli')
                        ->inline(false),
                    Toggle::make('is_final')
                        ->label('Döntő')
                        ->inline(false),
                    DateTimePicker::make('started_at')
                        ->label('Kezdés')
                        ->before(fn(Get $get) => $get('ended_at') === null ? null : 'ended_at')
                        ->native(false),
                    DateTimePicker::make('ended_at')
                        ->label('Befejezés')
                        ->after(fn(Get $get) => $get('started_at') === null ? null : 'started_at')
                        ->native(false),
                ])->columns()->visible(fn(Get $get) => $get('tournament_id') !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Verseny')
                    ->badge()
                    ->searchable(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Split::make([
                Grid::make(1)->schema([
                    \Filament\Infolists\Components\Section::make([
                        TextEntry::make('tournament.name')
                            ->label('Verseny')
                            ->badge(),
                        TextEntry::make('round')
                            ->label('Forduló'),
                        TextEntry::make('round_type')
                            ->label('Forduló típusa')
                            ->placeholder('Nincs beállítva')
                            ->state(fn(TournamentMatch $record) => match (RoundMode::tryFrom(collect($record->tournament->round_settings)->filter(fn($roundSettings) => $roundSettings['round'] === $record->round)->first()['mode'] ?? null)) {
                                RoundMode::ELIMINATION => 'Kieséses',
                                RoundMode::GROUP => 'Csoportos',
                                default => null,
                            }),
                    ])->columns()->grow(),
                    \Filament\Infolists\Components\Section::make('Csapatok')->schema([
                        TextEntry::make('homeTeam.name')
                            ->label('Hazai csapat')
                            ->color('info')
                            ->badge(),
                        TextEntry::make('home_team_score')
                            ->label('Hazai csapat pontszáma')
                            ->placeholder('Nincs megadva')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('awayTeam.name')
                            ->label('Vendég csapat')
                            ->color('danger')
                            ->badge(),
                        TextEntry::make('away_team_score')
                            ->label('Vendég csapat pontszáma')
                            ->placeholder('Nincs megadva')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('winner')
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
                            }),
                        TextEntry::make('score_difference')
                            ->label('Pontkülönbség')
                            ->placeholder('Nincs elég adat')
                            ->state(fn(TournamentMatch $record) => $record->home_team_score !== null && $record->away_team_score !== null ? abs($record->home_team_score - $record->away_team_score) : null),
                    ])->columns()->grow(),
                    \Filament\Infolists\Components\Section::make('Beállítások és időpontok')->schema([
                        IconEntry::make('is_stakeless')
                            ->label('Tét nélküli')
                            ->boolean(),
                        IconEntry::make('is_final')
                            ->label('Döntő')
                            ->boolean(),
                        TextEntry::make('started_at')
                            ->label('Kezdés')
                            ->placeholder('Nincs megadva')
                            ->dateTime(),
                        TextEntry::make('ended_at')
                            ->label('Befejezés')
                            ->placeholder('Nincs megadva')
                            ->dateTime(),
                        TextEntry::make('match_length')
                            ->label('Meccs hossza')
                            ->placeholder('Nincs elég adat')
                            ->state(fn(TournamentMatch $record) => $record->started_at !== null && $record->ended_at !== null ? $record->started_at->diff($record->ended_at)->format('%H:%I:%S') : null),
                    ])->columns()->grow(),
                ])->grow(),
                \Filament\Infolists\Components\Section::make([
                    TextEntry::make('created_at')
                        ->label('Létrehozva')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->label('Módosítva')
                        ->dateTime()
                ])->grow(false),
            ])->from('md')
        ])->columns(false);
    }

    public static function getWidgets(): array
    {
        return [
            ManageTournamentMatch::class
        ];
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
        return parent::getEloquentQuery()->ordered()->with(['homeTeam:id,name,tournament_id', 'awayTeam:id,name,tournament_id']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTournamentMatch::class,
            Pages\EditTournamentMatch::class,
        ]);
    }
}
