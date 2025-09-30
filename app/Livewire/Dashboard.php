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
    public function render()
    {
        return view('livewire.dashboard');
    }
    public function SwitchToHourlyReadings(){
        $this->option = "hourlySensorReadings";
    }
    public function SwitchToDailyReadings(){
        $this->option = "dailySensorReadings";
    }
    public function SwitchToAllReadings(){
        $this->option = "allSensorReadings";
    }
}
