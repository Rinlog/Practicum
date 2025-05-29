<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FrmSelectBox extends Component
{
    public $options = [];
    public $id = "";
    public function render()
    {
        return view('livewire..components.frm-select-box');
    }
}
