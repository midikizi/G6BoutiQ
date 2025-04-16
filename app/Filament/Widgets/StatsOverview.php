<?php

namespace App\Filament\Widgets;

use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Statistiques des ventes
        $ventesAujourdhui = Vente::whereDate('created_at', $today)->count();
        $ventesThisMonth = Vente::whereMonth('created_at', $thisMonth->month)->count();
        $montantAujourdhui = Vente::whereDate('created_at', $today)->sum('montant_total');
        $montantThisMonth = Vente::whereMonth('created_at', $thisMonth->month)->sum('montant_total');

        return [
            Stat::make('Ventes Aujourd\'hui', $ventesAujourdhui)
                ->description('Total des ventes du jour')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 3, 4, 5, 6, $ventesAujourdhui])
                ->color('success'),

            Stat::make('Chiffre d\'affaires du jour', number_format($montantAujourdhui, 2) . ' €')
                ->description('Total des revenus du jour')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, $montantAujourdhui])
                ->color('success'),

            Stat::make('Ventes du mois', $ventesThisMonth)
                ->description('Total des ventes du mois')
                ->descriptionIcon('heroicon-m-calendar')
                ->chart([7, 3, 4, 5, 6, $ventesThisMonth])
                ->color('primary'),

            Stat::make('Chiffre d\'affaires mensuel', number_format($montantThisMonth, 2) . ' €')
                ->description('Total des revenus du mois')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->chart([7, 3, 4, 5, 6, $montantThisMonth])
                ->color('primary'),
        ];
    }
} 