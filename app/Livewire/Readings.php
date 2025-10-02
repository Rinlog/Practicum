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
        $this->readingPage = session()->get("ReadingPage","deviceReadings");
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
