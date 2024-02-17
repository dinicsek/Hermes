<?php

namespace App\Providers;

use App\Helpers\AppLinking\AppLinkingHelper;
use App\Livewire\RegisterForTournament;
use App\Livewire\Tournaments;
use App\Livewire\UpcomingTournament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use LaraZeus\Boredom\BoringAvatar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Filament render hooks
        FilamentView::registerRenderHook(PanelsRenderHook::BODY_START, fn(): View => view('components.navigation', ['absolute' => true]), scopes: [Tournaments::class, RegisterForTournament::class, UpcomingTournament::class]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Pulse::user(fn($user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => app()->make(BoringAvatar::class)->get(name: $user->avatar_name),
        ]);

        Model::shouldBeStrict();

        $this->app->bind(AppLinkingHelper::class, fn() => new AppLinkingHelper());

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
