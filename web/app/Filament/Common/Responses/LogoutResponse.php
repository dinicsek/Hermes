<?php

namespace App\Filament\Common\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Session;


class LogoutResponse implements LogoutResponseContract
{

    public function toResponse($request)
    {
        Session::forget('url.intended');
        return redirect()->to(route('filament.common.auth.login'));
    }
}
