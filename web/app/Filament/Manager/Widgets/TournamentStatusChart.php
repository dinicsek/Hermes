<?php

namespace App\Filament\Manager\Widgets;

use App\Models\Tournament;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class TournamentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Versenyek állapoteloszlása';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $statusCounts = $this->getTournamentStatusCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Versenyek állapoteloszlása',
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                ]
            ],
            'labels' => array_keys($statusCounts),
        ];
    }

    protected function getTournamentStatusCounts(): array
    {
        $tournaments = Tournament::where('user_id', auth()->id())->get();

        $statusCounts = [];
        foreach ($tournaments as $tournament) {
            if (!isset($statusCounts[$tournament->status->getLabel()])) {
                $statusCounts[$tournament->status->getLabel()] = 0;
            }
            $statusCounts[$tournament->status->getLabel()]++;
        }

        return $statusCounts;
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'borderWidth' => 0,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
