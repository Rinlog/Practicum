<?php

namespace App\Livewire\Readings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Cache;
use \PDO;

class SensorReadings extends Component
{
    private $conn;

    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $TimeFrame = "LAST 7 DAYS";
    public $device;
    public $deviceInfo;
    public $devices = [];
    public $sensor;
    public $sensorInfo;
    public $sensors = [];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
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

            $this->devices = Cache::get("device")->where("organization_id",$this->OrgInfo->organization_id)->values()->toArray();

            if(count($this->devices) > 0){
                $this->device = $this->devices[0]->device_name;
                $this->deviceInfo = $this->devices[0];
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

    public function LoadSensorsBasedOnDevice(){
        try{

            $SensorAssoc = Cache::get("device_sensor_association")->where("device_eui",$this->deviceInfo->device_eui)->values()->toArray();

            $ArrayAssoc = [];
            foreach ($SensorAssoc as $Sensor){
                $ArrayAssoc[] = $Sensor->sensor_id;
            }

            if(count($ArrayAssoc) > 0){
               
                $this->sensors = Cache::get("sensor")->whereIn("sensor_id",$ArrayAssoc)->values()->toArray();
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
        }
        catch(Exception $e){
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
            $this->DisplayTableInfo = '';
            $this->StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$this->StartDate);
            $this->EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$this->EndDate);

            $sql = "SELECT split_part(sensor_reading_time::text,' ',1) AS date,
                           split_part(sensor_reading_time::text,' ',2) AS time,
                           device.device_name,
                           sensor.sensor_name,
                           sensor_type.sensor_type,
                           sensor_reading_data
                    FROM sensor_reading
                    JOIN device ON sensor_reading.device_eui = device.device_eui
                    JOIN sensor ON sensor_reading.sensor_id = sensor.sensor_id
                    JOIN sensor_type ON sensor.sensor_type_id = sensor_type.sensor_type_id
                    WHERE sensor_reading_time >= :start
                      AND sensor_reading_time <= :end
                      AND split_part(sensor_reading_time::text,' ',2) >= :stime
                      AND split_part(sensor_reading_time::text,' ',2) <= :etime
                      AND sensor_reading.device_eui = :eui
                      AND sensor_reading.sensor_id = :sid";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime,
                ":eui" => $this->deviceInfo->device_eui,
                ":sid" => $this->sensorInfo->sensor_id
            ]);

            $TableInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach($TableInfo as $Row){
                $this->DisplayTableInfo .= "
                <tr>
                <td></td>
                <td>".$Row->device_name."</td>
                <td>".$Row->sensor_type."</td>
                <td>".$Row->sensor_name."</td>
                <td>".$Row->date."</td>
                <td>".$Row->time."</td>
                <td>".$Row->sensor_reading_data."</td>
                </tr>
                ";
            }
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

    public function render()
    {
        return view('livewire.readings.sensor-readings');
    }
}
