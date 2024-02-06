<?php

namespace App\Filament\Common\Pages;

use App\Models\Enums\UserRole;
use Filament\Pages\Page;
use Illuminate\Routing\Redirector;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectToOwnPanel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.common.pages.redirect-to-own-panel';

    protected static ?string $slug = '';

    /**
     * Redirect users to their own panel based on their role.
     */
    public function boot(): \Illuminate\Foundation\Application|Redirector|RedirectResponse|Application
    {
        $user = auth()->user();

        $url = match ($user->role) {
            UserRole::ADMIN => '/admin',
            UserRole::MANAGER => '/manager',
        };

        return redirect($url);
    }
}
