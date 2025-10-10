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
        $AllowedPages = [];
        if (session()->get('browse_sensor_readings-sensor readings')){
            array_push($AllowedPages,"allSensorReadings");
        }
        if (session()->get('browse_sensor_readings-daily averages')){
            array_push($AllowedPages,"dailySensorReadings");
        }
        if (session()->get('browse_sensor_readings-hourly averages')){
            array_push($AllowedPages,"hourlySensorReadings");
        }
        if (in_array(session()->get("SensorReadingPage"),$AllowedPages)){
            $this->option = session()->get("SensorReadingPage","allSensorReadings");
        }
        else{
            $this->option = $AllowedPages[0];
        }
        
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
