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
        $AllowedPages = [];
        if (session()->get('logs-general log') or session()->get("IsSuperAdmin") == true){
            array_push($AllowedPages,"generalLog");
        }
        if (session()->get('logs-application-specific log') or session()->get("IsSuperAdmin") == true){
            array_push($AllowedPages,"applicationLog");
        }
        if (in_array(session()->get("LogsPage"),$AllowedPages)){
            $this->LogPage = session()->get("LogsPage","generalLog");
        }
        else{
            if (count($AllowedPages) > 0){
                $this->LogPage = $AllowedPages[0];
            }
        }
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
