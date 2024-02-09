<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\GroupResource\Pages;
use App\Filament\Manager\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Csoport';
    protected static ?string $pluralLabel = 'Csoportok';

    protected static ?string $navigationGroup = 'Általános';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->maxLength(255)
                    ->required(),
                Select::make('tournament_id')
                    ->label('Verseny')
                    ->relationship('tournament', 'name', modifyQueryUsing: function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                    ->searchable(['name'])
                    ->preload()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->required(),
                TextInput::make('round')
                    ->label('Forduló')
                    ->numeric()
                    ->default(1)
                    ->required(),
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
                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Verseny')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('round')
                    ->label('Forduló')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_generated')
                    ->label('Automatikusan generált')
                    ->boolean()
                    ->sortable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('tournament', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }
}
