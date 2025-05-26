<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title("Applications | IDL")]
class Applications extends Component
{
    public function render()
    {
        return view('livewire.applications');
    }
}
