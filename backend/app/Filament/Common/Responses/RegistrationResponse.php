<?php

namespace App\Filament\Common\Responses;

use App\Providers\RouteServiceProvider;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

class RegistrationResponse implements RegistrationResponseContract
{

    public function toResponse($request): \Illuminate\Foundation\Application|Redirector|RedirectResponse|Application|Response
    {
        return redirect(RouteServiceProvider::HOME);
    }
}
