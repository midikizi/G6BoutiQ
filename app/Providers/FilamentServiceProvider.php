<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\Modal;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('GestBouti')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->spa();
    }

    public function boot(): void
    {
        Filament::serving(function () {
            if (auth()->user()->hasRole('admin')) {
                Filament::registerNavigationGroups([
                    NavigationGroup::make()
                        ->label('Gestion des Produits')
                        ->icon('heroicon-o-shopping-bag')
                        ->collapsed(),
                    NavigationGroup::make()
                        ->label('Ventes')
                        ->icon('heroicon-o-shopping-cart')
                        ->collapsed(),
                    NavigationGroup::make()
                        ->label('Gestion des Stocks')
                        ->icon('heroicon-o-arrow-path')
                        ->collapsed(),
                    NavigationGroup::make()
                        ->label('Administration')
                        ->icon('heroicon-o-cog')
                        ->collapsed(),
                ]);
            } elseif (auth()->user()->hasRole('gerant')) {
                Filament::registerNavigationGroups([
                    NavigationGroup::make()
                        ->label('Gestion des Produits')
                        ->icon('heroicon-o-shopping-bag')
                        ->collapsed(),
                    NavigationGroup::make()
                        ->label('Ventes')
                        ->icon('heroicon-o-shopping-cart')
                        ->collapsed(),
                    NavigationGroup::make()
                        ->label('Gestion des Stocks')
                        ->icon('heroicon-o-arrow-path')
                        ->collapsed(),
                ]);
            } elseif (auth()->user()->hasRole('vendeur')) {
                Filament::registerNavigationGroups([
                    NavigationGroup::make()
                        ->label('Ventes')
                        ->icon('heroicon-o-shopping-cart')
                        ->collapsed(),
                ]);
            }
        });
    }
} 