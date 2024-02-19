<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Brickx\MaintenanceSwitch\MaintenanceSwitchPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use LaraZeus\Boredom\BoringAvatarPlugin;
use LaraZeus\Boredom\BoringAvatarsProvider;
use LaraZeus\Boredom\Enums\Variants;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->defaultAvatarProvider(
                BoringAvatarsProvider::class
            )
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Általános'),
                NavigationGroup::make('Rendszer'),
            ])
            ->navigationItems([
                NavigationItem::make('Horizon Vezérlőpult')
                    ->group('Rendszer')
                    ->icon('heroicon-o-server')
                    ->badge('Külső')
                    ->sort(10)
                    ->url(url(config('horizon.path')), shouldOpenInNewTab: true),
                NavigationItem::make('Pulse Vezérlőpult')
                    ->group('Rendszer')
                    ->icon('heroicon-o-chart-bar-square')
                    ->badge('Külső')
                    ->sort(20)
                    ->url(url(config('pulse.path')), shouldOpenInNewTab: true),
            ])
            ->plugins([
                BreezyCore::make()->myProfile()->enableTwoFactorAuthentication(),
                BoringAvatarPlugin::make()
                    ->variant(Variants::BEAM)
                    ->size(60)
                    ->square()
                    ->colors(['F6EDDC', 'E3E5D7', 'BDD6D2', 'A5C8CA', '586875']),
                MaintenanceSwitchPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make(),
                FilamentExceptionsPlugin::make()
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
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
            ]);
    }
}
