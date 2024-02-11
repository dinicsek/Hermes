<?php

namespace App\Providers;

use App\Helpers\AppLinking\AppLinkingHelper;
use Illuminate\Database\Eloquent\Model;
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
        //
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
    }
}
