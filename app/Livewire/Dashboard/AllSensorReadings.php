<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use \Exception;
use Illuminate\Support\Facades\Cache;
use \PDO;
class AllSensorReadings extends Component
{
    private $conn;
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
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
    public $readingHeaders = [];
    public $headers = [
        "DATE",
        "TIME",
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
        //getting locations from application + device types
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
        array_unshift($this->sensors,json_decode('{"sensor_id":"all","sensor_name":"All Sensors"}'));
    }
    public function LoadInfo($Input){
        $device = $Input[0];
        $sensor = $Input[1];
        $this->headers = [
            "DATE",
            "TIME",
        ];
        $sql = "SELECT 
                sensor_id,
                sensor_reading_data,
                split_part(sensor_reading_time::text,' ', 1) as date,
                split_part(sensor_reading_time::text,' ', 2) as time
            from sensor_reading
            WHERE 
            device_eui = :eui
            ".($sensor=="all"?"":"AND sensor_id = :sid\n").
            "AND sensor_reading_time >= :start
            AND sensor_reading_time <= :end
            AND split_part(sensor_reading_time::text,' ',2) >= :stime
            AND split_part(sensor_reading_time::text,' ',2) <= :etime";

        $stmnt = $this->conn->prepare($sql);
        if ($sensor == "all"){
            $stmnt->execute([
                ":eui" => $device,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime
            ]);
        }
        else{
            $stmnt->execute([
                ":eui" => $device,
                ":sid" => $sensor,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime
            ]);
        }
        $this->tableInfo = $stmnt->fetchAll(PDO::FETCH_OBJ);
        $this->groupedJsonReadings = [];
        $JsonData = [];
        $this->readingHeaders = [];
        //reading info + table info
        foreach ($this->tableInfo as $SensorInfo){
            $ReadingInfo = json_decode($SensorInfo->sensor_reading_data);
            array_push($JsonData,$ReadingInfo); //storing json data for later use
            if (array_key_exists($SensorInfo->date . $SensorInfo->time,$this->groupedJsonReadings)){
                array_push($this->groupedJsonReadings[$SensorInfo->date . $SensorInfo->time][1],$ReadingInfo);
            }
            else{
                $this->groupedJsonReadings += [$SensorInfo->date . $SensorInfo->time => [[$SensorInfo->date,$SensorInfo->time],[$ReadingInfo]]];
            }
        }
        //getting table headers
        foreach ($JsonData as $Reading){
            $Keys = array_keys((array) $Reading);
            foreach ($Keys as $Key){
                if (!(in_array(strtoupper($Key),$this->headers))){
                    array_push($this->headers,strtoupper($Key));
                    array_push($this->readingHeaders,$Key);
                }
            }
        }
        //verifying grouped reading length
        foreach ($this->groupedJsonReadings as $GroupedReading){
            $HeadersWeHave = [];
            foreach ($GroupedReading[1] as $Readings){
                foreach ($this->readingHeaders as $HeaderReading){
                    if (isset($Readings->{$HeaderReading})){
                        if (!(in_array($HeaderReading,$HeadersWeHave))){
                            array_push($HeadersWeHave,$HeaderReading);
                        }
                    }
                }
            }
            $HeadersNeeded = [];
            foreach ($this->readingHeaders as $AllHeader){
                if (!(in_array($AllHeader,$HeadersWeHave))){
                    array_push($HeadersNeeded,$AllHeader);
                }
            }
            if (count($HeadersNeeded) > 0){
                foreach ($HeadersNeeded as $HeaderNeeded){
                    $GroupedReading[1][0]->{$HeaderNeeded} = 0;
                }
            }
        }
    }
    public function LogExport(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION["User"]))) { return null; }
        DB::table("log")->insert([
            "log_activity_time"=>now(),
            "log_activity_type"=>"REPORT",
            "log_activity_performed_by"=> $_SESSION["User"]->user_username,
            "log_activity_desc"=>"Downloaded CSV of dashboard sensor readings"
        ]);
    }
    
    public function render()
    {
        return view('livewire.dashboard.all-sensor-readings');
    }
}
