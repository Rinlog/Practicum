<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FormChkbox extends Component
{
    public $text;
    public $id;
    public function render()
    {
        return view('livewire.components.form-chkbox');
    }
}
