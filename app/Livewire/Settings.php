<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
#[Title("Settings | IDL")]
class Settings extends Component
{
    public $settingToDisplay = "";

    public function mount($page){
        $this->settingToDisplay = $page;
    }
    public function render()
    {
        return view('livewire.settings');
    }

}
