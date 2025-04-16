<?php

namespace App\Filament\Widgets;

use App\Models\Produit;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'État des Stocks';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Produit::all();

        $labels = $data->pluck('nom')->toArray();
        $quantites = $data->pluck('quantite_stock')->toArray();
        $seuils = $data->pluck('seuil_alerte')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Quantité en stock',
                    'data' => $quantites,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // Bleu
                ],
                [
                    'label' => 'Seuil d\'alerte',
                    'data' => $seuils,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)', // Rouge
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Quantité'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Produits'
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