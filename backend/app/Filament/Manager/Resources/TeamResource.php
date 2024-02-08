<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\TeamResource\Pages;
use App\Filament\Manager\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split as InfoSplit;
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

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            InfoSplit::make([
                Grid::make(1)->schema([
                    Section::make([
                        TextEntry::make('name')
                            ->label('Név'),
                        TextEntry::make('tournament.name')
                            ->label('Verseny'),

                        IconEntry::make('is_approved')
                            ->label('Elfogadva')
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
                        ->dateTime(),
                    TextEntry::make('deleted_at')
                        ->label('Törölve')
                        ->placeholder("Nincs törölve.")
                        ->dateTime(),
                ])->grow(false),
            ])
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
