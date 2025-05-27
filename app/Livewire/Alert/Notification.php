<?php

namespace App\Livewire\Alert;

use Livewire\Component;

class Notification extends Component
{
    public $text = "Testing Text";
    public function render()
    {
        return view('livewire..alert.notification');
    }
}
