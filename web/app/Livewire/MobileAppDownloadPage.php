<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mobilalkalmazás letöltése')]
class MobileAppDownloadPage extends Component
{
    public function render()
    {
        return view('livewire.mobile-app-download-page');
    }
}
