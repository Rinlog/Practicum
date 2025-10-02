<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use \Exception;
use Illuminate\Support\Facades\Cache;
use \PDO;
class HourlyReadings extends Component
{
     private $conn;
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '0';
    public $EndTime = '23';
    public $TimeFrame = "TODAY";
    public $devices = [];
    public $deviceTypes = [];
    public $sensorRaw;
    public $sensors = [];
    public $applications = [];
    public $tableInfo = [];
    public $locations = [];
    public $subLocations = [];
    public $applicationLocationAssoc = [];
    public $applicationDeviceTypeAssoc = [];
    public $deviceDeploymentInfo = [];
    public $deviceSensorAssoc = [];
    public $headers = [
        "DATE",
        "HOUR",
    ];
    public $groupedJsonReadings = [];
    public function __construct(){
        $DB1 = config("database.connections.pgsql");
        $this->conn = new PDO(
            $DB1["driver"].":host=".$DB1["host"]." port=".$DB1["port"]." dbname=".$DB1["database"],
            $DB1["username"],
            $DB1["password"],
            [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
    public function LoadOptions(){
        //getting locations from application
        $this->applications = Cache::get("application",collect())->values()->toArray(); //loading applications
        $this->applicationLocationAssoc = Cache::get("application_location_association",collect())->values()->toArray();
        $this->applicationDeviceTypeAssoc = Cache::get("application_device_type_association",collect())->values()->toArray();
        //location proccessing
        $LocationRaw = Cache::get("location",collect());
        $SubLocationRaw = Cache::get("sub_location",collect());
        $this->locations = $LocationRaw->values()->toArray();
        array_unshift($this->locations,json_decode('{"location_name":"All Locations","location_id":"all"}'));
        $this->subLocations = $SubLocationRaw->values()->toArray();
        array_unshift($this->subLocations,json_decode('{"sub_location_name":"All Sub Locations","sub_location_id":"all"}'));


        //starts by loading all deployed devices associated with the locations above
        $this->deviceDeploymentInfo = Cache::get("device_deployment",collect())->values()->toArray();

        $RawDevices = Cache::get("device",collect());
        $this->devices = $RawDevices->values()->toArray();
        $this->deviceTypes = Cache::get("device_type",collect())
        ->values()
        ->toArray();
        array_unshift($this->deviceTypes,json_decode('{"device_type_id":"all","device_type":"All Device Types"}'));

        //load sensor info
        $this->deviceSensorAssoc = Cache::get("device_sensor_association",collect())->values()->toArray();
        $this->sensorRaw = Cache::get("sensor",collect());
        $this->sensors = $this->sensorRaw->values()->toArray();
    }
    public function LoadInfo($Input){
        $device = $Input[0];
        $sensor = $Input[1];
        $this->headers = [
            "DATE",
            "HOUR"
        ];
        $sql = "SELECT 
                sensor_id,
                hourly_readings_data,
                hourly_readings_counter,
                hourly_readings_date as date,
                hourly_readings_hour as hour
            from hourly_sensor_readings
            WHERE 
            device_eui = :eui
            ".($sensor=="all"?"":"AND sensor_id = :sid\n").
            "AND hourly_readings_date >= :start
            AND hourly_readings_date <= :end
            AND hourly_readings_hour >= :shour
            AND hourly_readings_hour <= :ehour";

        $stmnt = $this->conn->prepare($sql);
        if ($sensor == "all"){
            $stmnt->execute([
                ":eui" => $device,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":shour"=>$this->StartTime,
                ":ehour"=>$this->EndTime
            ]);
        }
        else{
            $stmnt->execute([
                ":eui" => $device,
                ":sid" => $sensor,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":shour"=>$this->StartTime,
                ":ehour"=>$this->EndTime
            ]);
        }
        $this->tableInfo = $stmnt->fetchAll(PDO::FETCH_OBJ);
        $this->groupedJsonReadings = [];
        $JsonData = [];
        foreach ($this->tableInfo as $SensorInfo){
            $ReadingInfo = json_decode($SensorInfo->hourly_readings_data);
            array_push($JsonData,$ReadingInfo); //storing json data for later use
            if (array_key_exists($SensorInfo->date . $SensorInfo->hour,$this->groupedJsonReadings)){
                array_push($this->groupedJsonReadings[$SensorInfo->date . $SensorInfo->hour][1],$ReadingInfo);
            }
            else{
                $this->groupedJsonReadings += [$SensorInfo->date . $SensorInfo->hour=> [[$SensorInfo->date, $SensorInfo->hour, $SensorInfo->hourly_readings_counter],[$ReadingInfo]]];
            }
        }
        foreach ($JsonData as $Reading){
            $Keys = array_keys((array) $Reading);
            foreach ($Keys as $Key){
                
                if (!(in_array(strtoupper($Key . " Average"),$this->headers))){
                    array_push($this->headers,strtoupper($Key . " Average"));
                    array_push($this->headers,strtoupper("Maximum " . $Key . " Time"));
                    array_push($this->headers,strtoupper("Maximum " . $Key . " Value"));
                    array_push($this->headers,strtoupper("Minimum " . $Key . " Time"));
                    array_push($this->headers,strtoupper("Minimum " . $Key . " Value"));
                }
            }
        }
    }
    public function render()
    {
        return view('livewire.dashboard.hourly-readings');
    }
}
