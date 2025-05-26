<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ReqUnderlineInput extends Component
{
    public $id = "";
    public $placeholder = "";
    public $type="";
    public $value="";
    public function render()
    {
        return view('livewire.components.req-underline-input');
    }
}
