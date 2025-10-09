<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FrmSelectWithSearch extends Component
{
    public $options = [];
    public $selectId = "";
    public $optionName = "";
    public $optionId = "";
    public $selectMessage;
    public $textColor = "";
    public function render()
    {
        return view('livewire.components.frm-select-with-search');
    }
}
