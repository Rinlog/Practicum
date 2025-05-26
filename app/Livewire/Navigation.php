<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
class Navigation extends Component
{
    public $width = "w-16";
    
    public $buttonPos = "left-10";
    public $buttonText = ">>";
    public $CurrentPage = "";
    public function render()
    {   
        return view('livewire.navigation');
    }
    public function ChangeSize($width){
        if ($width == 'w-16'){
            $this->width = "w-64";
            $this->buttonPos = "left-57";
            $this->buttonText = "<<";
        }
        else if ($width == "w-64"){
            $this->width = "w-16";
            $this->buttonPos = "left-10";
            $this->buttonText = ">>";
        }
    }
}
