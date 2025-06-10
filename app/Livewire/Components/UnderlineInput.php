<?php

namespace App\Livewire\Components;

use Livewire\Component;

class UnderlineInput extends Component
{
    public $id = "";
    public $placeholder = "";
    public $type="";
    public $value="";
    public $customStyles = "";
    public $borderColor = "border-[#32a3cf]";
    public $text;
    public $textColor = "";
    public $inputColor = "text-white";
    public function render()
    {
        return view('livewire..components.underline-input');
    }
}
