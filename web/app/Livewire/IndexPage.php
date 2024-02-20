<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kezdőlap')]
class IndexPage extends Component
{
    public function render()
    {
        return view('livewire.index-page');
    }
}
