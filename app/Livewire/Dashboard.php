<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use \Exception;
use Illuminate\Support\Facades\Cache;
use \PDO;
#[Title("Dashboard | IDL")]
class Dashboard extends Component
{
    public $option = "allSensorReadings";
    public $SessionOption;
    public function render()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->option = session()->get("SensorReadingPage","allSensorReadings");
        return view('livewire.dashboard');
    }
    public function SaveSession(){
        session()->put("SensorReadingPage",$this->option);
    }
    public function SwitchToHourlyReadings(){
        $this->option ="hourlySensorReadings";
        $this->SaveSession();
    }
    public function SwitchToDailyReadings(){
        $this->option ="dailySensorReadings";
        $this->SaveSession();
    }
    public function SwitchToAllReadings(){
        $this->option ="allSensorReadings";
        $this->SaveSession();
    }
}
