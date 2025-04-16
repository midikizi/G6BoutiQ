<?php

namespace App\Filament\Widgets;

use App\Models\Vente;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des Ventes';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(30, 0))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('Y-m-d');
        });

        $sales = Vente::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(montant_total) as total')
        ])
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $salesData = $days->map(function ($date) use ($sales) {
            return $sales[$date]->count ?? 0;
        });

        $revenueData = $days->map(function ($date) use ($sales) {
            return $sales[$date]->total ?? 0;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de ventes',
                    'data' => $salesData->values()->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Chiffre d\'affaires (€)',
                    'data' => $revenueData->values()->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'yAxisID' => 'revenue',
                ],
            ],
            'labels' => $days->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Nombre de ventes'
                    ]
                ],
                'revenue' => [
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Chiffre d\'affaires (€)'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
} 