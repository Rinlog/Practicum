<?php

namespace App\Livewire\Readings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;
class SensorReadings extends Component
{
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
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

    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = DB::table("organization")->where("organization_id", $_SESSION["User"]->organization_id)->firstOrFail();
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
            $organizations = DB::table("organization")->get();
            $this->Organizations = $organizations->toArray();
            $this->dispatch('$refresh');
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
            $this->devices = DB::table("device")->where("organization_id",$this->OrgInfo->organization_id)->get();
            $this->device = $this->devices[0]->device_name;
            $this->deviceInfo = $this->devices[0];
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
            $SensorAssoc = DB::table("device_sensor_association")->where("device_eui",$this->deviceInfo->device_eui)->get('sensor_id');
            $ArrayAssoc = [];
            foreach ($SensorAssoc as $Sensor){
                array_push($ArrayAssoc,$Sensor->sensor_id);
            }
            $this->sensors = DB::table("sensor")->whereIn('sensor_id',$ArrayAssoc)->get();
            if (count($this->sensors) > 0){
                $this->sensor = $this->sensors[0]->sensor_name;
                $this->sensorInfo = $this->sensors[0];
            }
            else{
                $this->sensor = "NO SENSORS";
                $this->sensorInfo = $this->sensors[0];
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

            $TableInfo = DB::table("sensor_reading")
            ->where("sensor_reading_time",">=",$this->StartDate)
            ->where("sensor_reading_time","<=",$this->EndDate)
            ->where(DB::raw("split_part(sensor_reading_time::text,' ',2)"),">=",$this->StartTime)
            ->where(DB::raw("split_part(sensor_reading_time::text,' ',2)"),"<=",$this->EndTime)
            ->where("sensor_reading.device_eui", $this->deviceInfo->device_eui)
            ->where("sensor_reading.sensor_id",$this->sensorInfo->sensor_id)
            ->join('device','sensor_reading.device_eui','=','device.device_eui')
            ->join("sensor", 'sensor_reading.sensor_id','=','sensor.sensor_id')
            ->join('sensor_type','sensor.sensor_type_id','=','sensor_type.sensor_type_id')
            ->get(DB::raw("split_part(sensor_reading_time::text,' ',1) AS date, split_part(sensor_reading_time::text,' ',2) as time, device.device_name, sensor.sensor_name, sensor_type.sensor_type, sensor_reading_data" ));

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
        DB::table("log")->insert([
            "log_activity_time"=>now(),
            "log_activity_type"=>"REPORT",
            "log_activity_performed_by"=> $_SESSION["User"]->user_username,
            "log_activity_desc"=>"Downloaded CSV of Sensor Reading Info"
        ]);
    }
    public function render()
    {
        return view('livewire..readings.sensor-readings');
    }
}
