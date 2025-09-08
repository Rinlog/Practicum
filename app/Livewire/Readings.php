<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title("Readings | IDL")]
class Readings extends Component
{
    public $readingPage = "deviceReadings";
    public function render()
    {
        return view('livewire.readings');
    }
}
