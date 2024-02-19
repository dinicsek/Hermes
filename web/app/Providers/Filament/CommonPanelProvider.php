<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use LaraZeus\Boredom\BoringAvatarPlugin;
use LaraZeus\Boredom\Enums\Variants;

class CommonPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('common')
            ->path('common')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverPages(in: app_path('Filament/Common/Pages'), for: 'App\\Filament\\Common\\Pages')
            ->plugins([
                BreezyCore::make()->myProfile()->enableTwoFactorAuthentication(),
                BoringAvatarPlugin::make()
                    ->variant(Variants::BEAM)
                    ->size(60)
                    ->square()
                    ->colors(['F6EDDC', 'E3E5D7', 'BDD6D2', 'A5C8CA', '586875']),
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
            ]);
    }
}
