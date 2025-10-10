<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title("Readings | IDL")]
class Readings extends Component
{
    public $readingPage = "deviceReadings";
    public function render()
    {
        $AllowedPages = [];
        if (session()->get('browse_readings-device readings')){
            array_push($AllowedPages,"deviceReadings");
        }
        if (session()->get('browse_readings-sensor readings')){
            array_push($AllowedPages,"sensorPage");
        }
        if (in_array(session()->get("ReadingPage"),$AllowedPages)){
            $this->readingPage = session()->get("ReadingPage","deviceReadings");
        }
        else{
            $this->readingPage = $AllowedPages[0];
        }
        return view('livewire.readings');
    }

    public function SaveSession(){
        session()->put("ReadingPage",$this->readingPage);
    }
    public function ShowDeviceReadings(){
        $this->readingPage = "deviceReadings";
        $this->SaveSession();
    }
    public function ShowSensorReadings(){
        $this->readingPage = "sensorPage";
        $this->SaveSession();
    }
}
