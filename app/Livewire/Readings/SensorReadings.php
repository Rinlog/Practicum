<?php

namespace App\Livewire\Readings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use \Exception;
use Illuminate\Support\Facades\Cache;
use \PDO;
use \DateTime;
class SensorReadings extends Component
{
    private $conn;

    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $TimeFrame = "TODAY";
    public $device;
    public $deviceInfo;
    public $devices = [];
    public $sensor;
    public $sensorInfo;
    public $sensorTypeInfo;
    public $sensors = [];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $TableInfo = [];
    public $headers = [
        "DEVICE NAME",
        "SENSOR TYPE",
        "SENSOR NAME",
        "DATE",
        "TIME",
        "READING"
    ];
    public $DisplayTableInfo = "";

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

    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = collect(Cache::get('organization', collect()))
                    ->firstWhere("organization_id", $_SESSION["User"]->organization_id);
                $this->organization = $organizationInfo->organization_name;
                $this->OrgInfo = $organizationInfo;
            }
            catch(Exception $e){
                $this->organization = "";
            }
        }
    }

    public function LoadOrganizations(){
        try{
            $this->Organizations = Cache::get("organization",collect())->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SetOrg($NewOrgID){
        $NewOrg = [];
        foreach ($this->Organizations as $org){
            if ($org->organization_id == $NewOrgID) {
                $NewOrg = $org;
            }
        }
        $this->OrgInfo = $NewOrg;
        $this->organization = $NewOrg->organization_name;
    }

    public function LoadDevicesBasedOnOrg(){
        try{

            $this->devices = Cache::get("device", collect())->where("organization_id",$this->OrgInfo->organization_id)->values()->toArray();

            if(count($this->devices) > 0){
                $this->device = $this->devices[0]->device_name;
                $this->deviceInfo = $this->devices[0];
            }
            else{
                $this->device = "NONE";
                $this->deviceInfo = null;
            }
            $this->LoadSensorsBasedOnDevice();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SetDevice($NewDeviceEUI){
        try{
            foreach ($this->devices as $device){
                if ($device->device_eui == $NewDeviceEUI) {
                    $this->device = $device->device_name;
                    $this->deviceInfo = $device;
                    $this->LoadSensorsBasedOnDevice();
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadSensorTypeInfo(){
        try{
            $this->sensorTypeInfo = Cache::get("sensor_type", collect())->where("sensor_type_id",$this->sensorInfo->sensor_type_id)->values()->toArray()[0];
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadSensorsBasedOnDevice(){
        try{

            $SensorAssoc = Cache::get("device_sensor_association", collect())->where("device_eui",$this->deviceInfo->device_eui)->values()->toArray();

            $ArrayAssoc = [];
            foreach ($SensorAssoc as $Sensor){
                $ArrayAssoc[] = $Sensor->sensor_id;
            }

            if(count($ArrayAssoc) > 0){
               
                $this->sensors = Cache::get("sensor", collect())->whereIn("sensor_id",$ArrayAssoc)->values()->toArray();
            } else {
                $this->sensors = [];
            }

            if (count($this->sensors) > 0){
                $this->sensor = $this->sensors[0]->sensor_name;
                $this->sensorInfo = $this->sensors[0];
            }
            else{
                $this->sensor = "NO SENSORS";
                $this->sensorInfo = null;
            }
            $this->LoadSensorTypeInfo();
        }
        catch(Exception $e){
            $this->sensor = "NO SENSORS";
            $this->sensorInfo = null;
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SetSensor($NewSensorID){
        try{
            foreach ($this->sensors as $Sensor){
                if ($Sensor->sensor_id == $NewSensorID){
                    $this->sensorInfo = $Sensor;
                    $this->sensor = $Sensor->sensor_name;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function LoadInfo(){
        try{
            $this->StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$this->StartDate);
            $this->EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$this->EndDate);
            $this->StartDate = preg_replace("/\"/","",$this->StartDate);
            $this->EndDate = preg_replace("/\"/","",$this->EndDate);

            $sql = "SELECT 
                sensor_reading_data,
                split_part(sensor_reading_time::text,' ', 1) as date,
                split_part(sensor_reading_time::text,' ', 2) as time
            from sensor_reading
            WHERE 
            device_eui = :eui
            AND sensor_id = :sid
            AND sensor_reading_time >= :start
            AND sensor_reading_time <= :end
            AND split_part(sensor_reading_time::text,' ',2) >= :stime
            AND split_part(sensor_reading_time::text,' ',2) <= :etime";

            $stmnt = $this->conn->prepare($sql);

            $stmnt->execute([
                ":eui" => $this->deviceInfo->device_eui,
                ":sid" => $this->sensorInfo->sensor_id,
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime
            ]);
            $this->TableInfo = $stmnt->fetchAll(PDO::FETCH_OBJ);
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function LogExport(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION["User"]))) { return null; }

        $stmt = $this->conn->prepare("INSERT INTO log (log_activity_time, log_activity_type, log_activity_performed_by, log_activity_desc) VALUES (NOW(), 'REPORT', :by, :desc)");
        $stmt->execute([
            ":by" => $_SESSION["User"]->user_username,
            ":desc" => "Downloaded CSV of Sensor Reading Info"
        ]);
    }
    public function LoadSmallStuff(){
        try{    
            $this->LoadUsersOrganization();
            $this->LoadDevicesBasedOnOrg();
            $this->LoadOrganizations();
        }
        catch(Exception $e){
            
        }
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
            $PermsDetailed = session()->get("browse_readings-sensor readings");
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
        return view('livewire.readings.sensor-readings');
    }
}
