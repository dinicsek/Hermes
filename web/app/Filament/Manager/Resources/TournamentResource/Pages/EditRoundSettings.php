<?php

namespace App\Filament\Manager\Resources\TournamentResource\Pages;

use App\Filament\Manager\Resources\TournamentResource;
use App\Models\Data\RoundConfiguration;
use App\Models\Enums\EventStatus;
use App\Models\Enums\RoundMode;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Str;

class EditRoundSettings extends Page
{
    use InteractsWithRecord;
    use InteractsWithForms;
    use InteractsWithFormActions;
    use HasUnsavedDataChangesAlert;

    protected static string $resource = TournamentResource::class;

    protected static string $view = 'filament.manager.resources.tournament-resource.pages.edit-round-settings';

    protected static ?string $navigationLabel = 'Fordulók beállításai';
    protected static ?string $navigationIcon = 'custom-brackets';

    protected static ?string $title = 'Fordulók beállításai';

    public ?array $data;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill([
            'rounds' => $this->record->round_settings->toCollection()->mapWithKeys(function ($item) {
                return [Str::uuid()->toString() => [
                    'mode' => $item->mode,
                    'groupCount' => $item->groupCount ?? 2,
                    'advancingCount' => $item->advancingCount ?? 2,
                    'eliminationLevels' => $item->eliminationLevels ?? 1,
                ]];
            }),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('rounds')->schema([
                    Select::make('mode')
                        ->label('Mód')
                        ->hintIcon('heroicon-o-information-circle')
                        ->hintIconTooltip(fn(Get $get) => match ($get('mode')) {
                            RoundMode::GROUP => 'Csoportos mód esetén a csapatokat csoportokba osztjuk, majd a csoportokból megadott számú csapat jut tobább.',
                            RoundMode::ELIMINATION => 'Kieséses mód esetén a csapatok egymás ellen játszanak, a vesztes csapatok kiesnek.',
                        })
                        ->options(RoundMode::class)
                        ->default(RoundMode::GROUP)
                        ->required()
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->live(),
                    TextInput::make('groupCount')
                        ->label('Csoportok száma')
                        ->numeric()
                        ->default(2)
                        ->minValue(1)
                        ->visible(fn(Get $get) => $get('mode') === RoundMode::GROUP)
                        ->required(fn(Get $get) => $get('mode') === RoundMode::GROUP),
                    TextInput::make('advancingCount')
                        ->label('Továbbjutók száma')
                        ->numeric()
                        ->default(2)
                        ->minValue(1)
                        ->visible(fn(Get $get) => $get('mode') === RoundMode::GROUP)
                        ->required(fn(Get $get) => $get('mode') === RoundMode::GROUP),
                    TextInput::make('eliminationLevels')
                        ->label('Kiesési szintek')
                        ->helperText('Ennyiszer kell egy csapatnak veszítenie ahhoz, hogy kiessen')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->visible(fn(Get $get) => $get('mode') === RoundMode::ELIMINATION)
                        ->required(fn(Get $get) => $get('mode') === RoundMode::ELIMINATION)
                ])
                    ->label('Fordulók')
                    ->addActionLabel('Forduló hozzáadása')
                    ->collapsible()
                    ->reorderableWithButtons()
                    ->minItems(1)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if (count($value) < 1) {
                                    return;
                                }

                                $rounds = collect($value);
                                $eliminationRounds = $rounds->filter(fn($round) => $round['mode'] === RoundMode::ELIMINATION);

                                if ($rounds->last()['mode'] === RoundMode::ELIMINATION) {
                                    $eliminationRounds->pop();
                                }

                                if (!$eliminationRounds->isEmpty()) {
                                    $fail('Minden kieséses forduló csak egyetlen továbbjutó (győztes) csapatot eredményez, így csak az utolsó forsuló lehet kieséses.');
                                }
                            };
                        }
                    ])
                    ->validationMessages([
                        'min' => 'Legalább egy fordulót meg kell adni.',
                    ]),
            ])
            ->statePath('data')
            ->disabled(fn(EditRoundSettings $livewire) => $livewire->record->status !== EventStatus::UPCOMING);
    }

    public function save()
    {
        $this->form->validate();

        $data = $this->data['rounds'];
        $processedData = [];

        $index = 1;
        foreach ($data as $round) {
            $processedData[] = new RoundConfiguration(
                round: $index,
                mode: $round['mode'],
                groupCount: $round['mode'] === RoundMode::GROUP ? $round['groupCount'] : null,
                advancingCount: $round['mode'] === RoundMode::GROUP ? $round['advancingCount'] : null,
                eliminationLevels: $round['mode'] === RoundMode::ELIMINATION ? $round['eliminationLevels'] : null,
            );
            $index++;
        }

        $this->rememberData();

        $this->record->update([
            'round_settings' => RoundConfiguration::collection($processedData),
        ]);

        Notification::make()->success()->title('Sikeresen mentve!')->send();
    }

    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function getSaveFormAction()
    {
        return Action::make('Mentés')
            ->action('save')
            ->keyBindings(['mod+s'])
            ->hidden(fn(EditRoundSettings $livewire) => $livewire->record->status !== EventStatus::UPCOMING);
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }
}
