<?php

namespace App\Filament\Manager\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Csoportok';

    protected static ?string $modelLabel = 'Csoport';

    protected static ?string $pluralLabel = 'Csoportok';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Név')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('round')
                    ->label('Forduló')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->sortable(),
                Tables\Columns\TextColumn::make('round')
                    ->label('Forduló')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_generated')
                    ->label('Automatikusan generált')
                    ->boolean()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(function (array $data, string $model) {
                    $data['is_generated'] = false;
                    $data['tournament_id'] = $this->ownerRecord->tournament_id;
                    return $this->ownerRecord->groups()->create($data);
                }),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->where('tournament_id', $this->ownerRecord->tournament_id)->select('groups.id', 'groups.name', 'groups.is_generated')),
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

    #[On('refresh_groups')]
    public function refresh()
    {
    }
}
