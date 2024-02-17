<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use LaraZeus\Boredom\BoringAvatar;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Általános';

    protected static ?string $modelLabel = 'Felhasználó';

    protected static ?string $pluralLabel = 'Felhasználók';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->required(),
                TextInput::make('email')
                    ->label('E-mail cím')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique('users', 'email', ignoreRecord: true),
                TextInput::make('password')
                    ->label('Jelszó')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->maxLength(255),
                Select::make('role')
                    ->label('Szerepkör')
                    ->required()
                    ->options(UserRole::class)
                    ->default(UserRole::MANAGER)
                    ->native(false)
                    ->selectablePlaceholder(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->state(fn(User $record) => app()->make(BoringAvatar::class)->get(name: $record->name))
                    ->label('Profilkép')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail cím')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('E-mail megerősítve')
                    ->placeholder('Nincs megerősítve')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Szerepkör')
                    ->badge()
                    ->searchable()
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
                Section::make([
                    TextEntry::make('name')->label('Név'),
                    TextEntry::make('email')->label('E-mail cím'),
                    TextEntry::make('email_verified_at')
                        ->label('E-mail megerősítve')
                        ->dateTime()
                        ->placeholder('Nincs megerősítve'),
                    TextEntry::make('role')
                        ->label('Szerepkör')
                        ->badge(),
                ])->columns()->grow(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
        ]);
    }
}
