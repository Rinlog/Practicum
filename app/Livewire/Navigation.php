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
    {   $this->LoadPerms();
        return view('livewire.navigation');
    }
    public $ShowSensorReadings = false;
    public $ShowReadings = false;
    public $ShowLogs = false;
    public $ShowSettings = false;
    public $SettingPages = []; //holds all permissions associated with settings
    function LoadPerms(){
        $Permissions = session()->get("AllAppPermsForUser");
        $ComponenentOfInterest = session()->get("AdminComponentID");
        foreach ($Permissions as $Permission){
            if ($Permission->component_id == $ComponenentOfInterest){
                if (strtolower($Permission->resource_name) == "settings"){
                    $this->ShowSettings = true;
                    array_push($this->SettingPages,$Permission);
                }
                else if (strtolower($Permission->resource_name) == "logs"){
                    $this->ShowLogs = true;
                }
                else if (strtolower($Permission->resource_name) == "browse readings"){
                    $this->ShowReadings = true;
                }
                else if (strtolower($Permission->resource_name) == "browse sensor readings"){ //using browse sensor readings to display dashboard.
                    $this->ShowSensorReadings = true;
                }
            }
        }
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
