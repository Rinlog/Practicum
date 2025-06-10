<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title("Logs | IDL")]
class Logs extends Component
{
    public $LogPage = "generalLog";
    public function render()
    {
        return view('livewire.logs');
    }
}
