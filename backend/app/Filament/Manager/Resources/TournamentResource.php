<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TournamentResource\Pages;
use App\Filament\Manager\Resources\TournamentResource\RelationManagers;
use App\Models\Tournament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Parallax\FilamentComments\Infolists\Components\CommentsEntry;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $modelLabel = 'Verseny';

    protected static ?string $pluralLabel = 'Versenyek';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(2),
                Textarea::make('description')
                    ->label('Leírás')
                    ->required()
                    ->columnSpan(2),
                \Filament\Forms\Components\Section::make('Időpontok')->schema([
                    DateTimePicker::make('registration_starts_at')
                        ->label('Regisztráció kezdete')
                        ->required()
                        ->before('registration_ends_at')
                        ->native(false),
                    DateTimePicker::make('registration_ends_at')
                        ->label('Regisztráció vége')
                        ->required()
                        ->after('registration_starts_at')
                        ->native(false),
                    DateTimePicker::make('starts_at')
                        ->label('Kezdés')
                        ->required()
                        ->before(fn(Get $get) => $get('ended_at') === null ? null : 'ended_at')
                        ->native(false),
                    DateTimePicker::make('ended_at')
                        ->label('Lezárult')
                        ->placeholder('Nem zárult le')
                        ->hiddenOn('create')
                        ->after('starts_at')
                        ->native(false),
                ])->columns(),
                \Filament\Forms\Components\Section::make('Beállítások')->schema([
                    TextInput::make('min_team_size')
                        ->label('Minimális csapatméret')
                        ->suffix('fő')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->required(),
                    TextInput::make('max_team_size')
                        ->label('Maximális csapatméret')
                        ->suffix('fő')
                        ->numeric()
                        ->default(6)
                        ->minValue(1)
                        ->required(),
                    TextInput::make('max_teams')
                        ->label('Csapatok maximális száma')
                        ->placeholder('Nincs korlátozva')
                        ->numeric()
                        ->minValue(2),
                    Toggle::make('end_when_matches_concluded')
                        ->label('Lezárás meccsek befejezésekor')
                        ->inline(false),
                ])->columns()->description(fn(string $operation) => match ($operation) {
                    'create' => 'A további beállítások a létrehozás után a \'Fordulók beállításai\' menüpontban érhetők el.',
                    'edit' => 'A további beállítások a \'Fordulók beállításai\' menüpontban érhetők el.',
                    default => null,
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kód')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Állapot')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_starts_at')
                    ->label('Regisztráció kezdete')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_ends_at')
                    ->label('Regisztráció vége')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_teams')
                    ->label('Csapatok maximális száma')
                    ->placeholder('Nincs korlátozva')
                    ->sortable()
                    ->searchable()
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
            ])
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
                        TextEntry::make('code')
                            ->copyable()
                            ->label('Kód'),
                        TextEntry::make('description')
                            ->label('Leírás')
                            ->columnSpan(2),
                        TextEntry::make('status')
                            ->label('Állapot')
                            ->badge(),
                    ])->columns()->grow(),
                    Section::make('Időpontok')->schema([
                        TextEntry::make('registration_starts_at')
                            ->label('Regisztráció kezdete')
                            ->dateTime(),
                        TextEntry::make('registration_ends_at')
                            ->label('Regisztráció vége')
                            ->dateTime(),
                        TextEntry::make('starts_at')
                            ->label('Kezdés')
                            ->dateTime(),
                        TextEntry::make('ended_at')
                            ->label('Lezárult')
                            ->placeholder('Még nem zárult le')
                            ->dateTime(),
                    ])->columns()->grow(),
                    Section::make('Beállítások')->schema([
                        TextEntry::make('min_team_size')
                            ->label('Minimális csapatméret')
                            ->suffix(' fő'),
                        TextEntry::make('max_team_size')
                            ->label('Maximális csapatméret')
                            ->suffix(' fő'),
                        TextEntry::make('max_teams')
                            ->label('Csapatok maximális száma')
                            ->placeholder('Nincs korlátozva'),
                        IconEntry::make('end_when_matches_concluded')
                            ->label('Lezárás mérkőzések befejezésekor')
                            ->boolean(),
                    ])->columns()->grow(),
                    CommentsEntry::make('filament_comments')
                ])->grow(),
                Section::make([
                    TextEntry::make('created_at')
                        ->label('Létrehozva')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->label('Módosítva')
                        ->dateTime(),
                ])->grow(false),
            ])->from('md')
        ])->columns(false);
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
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'view' => Pages\ViewTournament::route('/{record}'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
            'edit-round-settings' => Pages\EditRoundSettings::route('/{record}/edit-round-settings'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('user_id', auth()->id());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTournament::class,
            Pages\EditTournament::class,
            Pages\EditRoundSettings::class,
        ]);
    }
}
