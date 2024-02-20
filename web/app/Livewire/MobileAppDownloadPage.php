<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mobilalkalmazás letöltése')]
class MobileAppDownloadPage extends Component
{
    public function render()
    {
        return view('livewire.mobile-app-download-page');
    }

    public function downloadApp()
    {
        return Storage::disk('public')->download('hermes.apk', 'hermes.apk');
    }
}
