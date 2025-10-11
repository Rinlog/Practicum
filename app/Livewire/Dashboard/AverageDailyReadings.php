<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use \Exception;
use Illuminate\Support\Facades\Cache;
use Mockery\Undefined;
use \PDO;
class AverageDailyReadings extends Component
{
    private $conn;
    public $StartDate = '';
    public $EndDate = '';
    public $TimeFrame = "LAST 30 DAYS";
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
    ];
    public $dataItems = [];
    public $groupedJsonReadings = [];
    public $normalDisplay = true;
    public $displayDataItems = false;
    public $dataItemList = [];
    public $selectedDataItem = null;
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
        $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", session("User")->user_id);

            $ApplicationsArray = $userRoles->pluck("application_id")->all();
            if (count($ApplicationsArray) > 0){
                $this->applications = Cache::get("application", collect())
                    ->whereIn("application_id", $ApplicationsArray);
            }
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
        ];
        $sql = "SELECT 
                sensor_id,
                daily_readings_counter,
                daily_readings_data,
                daily_readings_date as date
            from daily_sensor_readings
            WHERE 
            device_eui = :eui
            ".($sensor=="all"?"":"AND sensor_id = :sid\n").
            "AND daily_readings_date >= :start
            AND daily_readings_date <= :end";

        $stmnt = $this->conn->prepare($sql);
        if ($sensor == "all"){
            $stmnt->execute([
                ":eui" => $device,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
            ]);
        }
        else{
            $stmnt->execute([
                ":eui" => $device,
                ":sid" => $sensor,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
            ]);
        }
        $this->tableInfo = $stmnt->fetchAll(PDO::FETCH_OBJ);
        $this->groupedJsonReadings = [];
        $JsonData = [];
        foreach ($this->tableInfo as $SensorInfo){
            $ReadingInfo = json_decode($SensorInfo->daily_readings_data);
            array_push($JsonData,$ReadingInfo); //storing json data for later use
            if (array_key_exists($SensorInfo->date,$this->groupedJsonReadings)){
                array_push($this->groupedJsonReadings[$SensorInfo->date][1],$ReadingInfo);
            }
            else{
                $this->groupedJsonReadings += [$SensorInfo->date=> [[$SensorInfo->date,$SensorInfo->daily_readings_counter],[$ReadingInfo]]];
            }
        }
        //check if data items exist
        $this->normalDisplay = true; //this is used for certain tables that don't follow the normal total,maximum,minimum entrys inside the reading
        $this->displayDataItems = false;
        if (count($this->groupedJsonReadings) > 0){
            $keys = array_keys($this->groupedJsonReadings);
            $InnerValueKeys = array_keys((array) $this->groupedJsonReadings[$keys[0]][1][0]);
            $FirstInnerValueSectionCount = count((array) $this->groupedJsonReadings[$keys[0]][1][0]->{$InnerValueKeys[0]});
            if ($FirstInnerValueSectionCount  > 3){
                //verifying info is what is expected
                if (str_contains(strtolower($InnerValueKeys[0]),"all_regions")){
                    $this->normalDisplay = false;
                    $this->displayDataItems = true;
                }
            }
            elseif ($FirstInnerValueSectionCount == 3){
                if ($InnerValueKeys[0] == "total"){
                    $this->normalDisplay = true;
                }
            }
            elseif ($FirstInnerValueSectionCount < 3){
                //verifying info is what is expected
                if ($InnerValueKeys[0] == "total_exited"){
                    $this->normalDisplay = false;
                }
            }
            
        }
        //loading in headers, loading them in depends on the type of sensor info, hence "normalDisplay" 
        //is used which refers to which one i was exposed to first,
        //then the second option i considered not normal.
        $AlreadyAddedHeaders = false; //this is used for the data-items page
        foreach ($JsonData as $Reading){
                if (is_object($Reading) && $this->normalDisplay == true){
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
                else if ($this->normalDisplay == false){
                    if (is_object($Reading)){
                        $Keys = array_keys((array) $Reading);
                        if ($this->displayDataItems == false){
                            if (!(in_array(strtoupper($Keys[0]),$this->headers))){
                                foreach ($Keys as $Key){
                                    if (str_contains(strtolower($Key), "maximum") || str_contains(strtolower($Key),"minimum")){
                                        array_push($this->headers,strtoupper($Key." time"));
                                        array_push($this->headers,strtoupper($Key." value"));
                                    }
                                    else{
                                        array_push($this->headers,strtoupper($Key));
                                    }
                                }
                            }
                        }
                        else if ($this->displayDataItems == true){
                            if ($this->selectedDataItem == null){
                                $this->selectedDataItem = $Keys[0];
                            }
                            foreach ($Keys as $Key){
                                if (!(in_array($Key,$this->dataItems))){
                                    array_push($this->dataItems,$Key);
                                }
                                if ($Key = $this->selectedDataItem){
                                    if ($AlreadyAddedHeaders == false){
                                        $AlreadyAddedHeaders = true;
                                        foreach($Reading->{$Key} as $InnerReadingKey=>$InnerReading){
                                            if (str_contains(strtolower($InnerReadingKey), "maximum") || str_contains(strtolower($InnerReadingKey),"minimum")){
                                                array_push($this->headers,strtoupper($InnerReadingKey." time"));
                                                array_push($this->headers,strtoupper($InnerReadingKey." value"));
                                            }
                                            else{
                                                array_push($this->headers,strtoupper($InnerReadingKey));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
        }
    }
    public function LogExport(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(session()->get("User"))) { return null; }
        DB::table("log")->insert([
            "log_activity_time"=>now(),
            "log_activity_type"=>"REPORT",
            "log_activity_performed_by"=> session()->get("User")->user_username,
            "log_activity_desc"=>"Downloaded CSV of dashboard average daily sensor readings"
        ]);
    }
    public $Perms = [
        "create"=>false,
        "read"=>false,
        "update"=>false,
        "delete"=>false,
        "report"=>false
    ];
    public function LoadPagePerms(){
        try{
            $PermsDetailed = session()->get("browse_sensor_readings-daily averages");
            foreach ($PermsDetailed as $Perm){
                if ($Perm->permission_create == true){
                    $this->Perms["create"] = true;
                }
                if ($Perm->permission_read == true){
                    $this->Perms["read"] = true;
                }
                if ($Perm->permission_update == true){
                    $this->Perms["update"] = true;
                }
                if ($Perm->permission_delete == true){
                    $this->Perms["delete"] = true;
                }
                if ($Perm->permission_report == true){
                    $this->Perms["report"] = true;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function render()
    {
        $this->LoadPagePerms();
        return view('livewire..dashboard.average-daily-readings');
    }
}
