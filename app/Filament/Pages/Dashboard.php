<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Vente;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroups(): array
    {
        return [
            NavigationGroup::make()
                ->label('Gestion des Produits')
                ->icon('heroicon-o-shopping-bag')
                ->items([
                    NavigationItem::make('Produits')
                        ->icon('heroicon-o-cube')
                        ->url(route('filament.admin.resources.produits.index'))
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.produits.*')),
                    NavigationItem::make('Categories')
                        ->icon('heroicon-o-tag')
                        ->url(route('filament.admin.resources.categories.index'))
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.categories.*')),
                ]),
            NavigationGroup::make()
                ->label('Gestion des Ventes')
                ->icon('heroicon-o-currency-dollar')
                ->items([
                    NavigationItem::make('Ventes')
                        ->icon('heroicon-o-shopping-cart')
                        ->url(route('filament.admin.resources.ventes.index')),
                    NavigationItem::make('Clients')
                        ->icon('heroicon-o-users')
                        ->url(route('filament.admin.resources.clients.index')),
                ]),
            NavigationGroup::make()
                ->label('Gestion des Stocks')
                ->icon('heroicon-o-cube')
                ->items([
                    NavigationItem::make('Mouvements de Stock')
                        ->icon('heroicon-o-arrow-path')
                        ->url(route('filament.admin.resources.mouvement-stocks.index')),
                ]),
        ];
    }

    public function getWidgets(): array
    {
        $widgets = [];

        // Widget pour l'administrateur
        if (auth()->user()->hasRole('admin')) {
            $widgets[] = StatsOverviewWidget::class;
        }

        // Widget pour le gestionnaire
        if (auth()->user()->hasRole('gestionnaire')) {
            $widgets[] = GestionnaireStatsWidget::class;
            $widgets[] = StockAlerteWidget::class;
            $widgets[] = ClientsWidget::class;
        }

        // Widget pour le vendeur
        if (auth()->user()->hasRole('vendeur')) {
            $widgets[] = VendeurStatsWidget::class;
        }

        return $widgets;
    }
}

class GestionnaireStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Card::make('Produits en stock', Produit::sum('quantite_stock'))
                ->description('Total des produits')
                ->descriptionIcon('heroicon-s-archive')
                ->color('success'),
            Card::make('Produits en alerte', Produit::whereRaw('quantite_stock <= seuil_alerte')->count())
                ->description('Nécessitent un réapprovisionnement')
                ->descriptionIcon('heroicon-s-exclamation')
                ->color('danger'),
            Card::make('Clients actifs', Client::count())
                ->description('Nombre total de clients')
                ->descriptionIcon('heroicon-s-users')
                ->color('primary'),
        ];
    }
}

class StockAlerteWidget extends BaseTableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Produit::query()
            ->whereRaw('quantite_stock <= seuil_alerte')
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nom')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantite_stock')
                ->sortable(),
            Tables\Columns\TextColumn::make('seuil_alerte')
                ->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }
}

class ClientsWidget extends BaseTableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Client::query()
            ->withCount('ventes')
            ->orderByDesc('ventes_count');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nom')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('ventes_count')
                ->label('Nombre de ventes')
                ->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }
}

class VendeurStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Card::make('Ventes du jour', Vente::whereDate('created_at', today())->count())
                ->description('Nombre de ventes aujourd\'hui')
                ->descriptionIcon('heroicon-s-shopping-cart')
                ->color('success'),
            Card::make('Montant total du jour', Vente::whereDate('created_at', today())->sum('montant_total'))
                ->description('Chiffre d\'affaires du jour')
                ->descriptionIcon('heroicon-s-currency-euro')
                ->color('success'),
            Card::make('Ventes en attente', Vente::where('statut', 'en_cours')->count())
                ->description('Ventes à finaliser')
                ->descriptionIcon('heroicon-s-clock')
                ->color('warning'),
        ];
    }
} 