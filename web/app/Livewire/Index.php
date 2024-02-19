<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kezdőlap')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.index');
    }
}
