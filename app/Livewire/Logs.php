<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title("Logs | IDL")]
class Logs extends Component
{
    public $LogPage = "generalLog";
    public function render()
    {
        $this->LogPage = session()->get("LogsPage","generalLog");
        return view('livewire.logs');
    }

    public function SaveSession(){
        session()->put("LogsPage",$this->LogPage);
    }
    public function ShowGeneralLog(){
        $this->LogPage = "generalLog";
        $this->SaveSession();
    }
    public function ShowAppLog(){
        $this->LogPage = "applicationLog";
        $this->SaveSession();
    }
}
