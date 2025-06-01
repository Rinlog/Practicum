<?php

namespace App\Livewire\Components;
 use Livewire\Attributes\Reactive;
use Livewire\Component;

class FrmSelectBox extends Component
{
    #[Reactive]
    public $options = [];
    public $id = "";
    #[Reactive]
    public $optionName = "";
    public $onChange = "";
    public function render()
    {
        return view('livewire..components.frm-select-box');
    }
}
