<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;

class DeviceSensorAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "DEVICE NAME",
        "SENSOR TYPE",
        "SENSOR NAME",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $user = "";
    public $DisplayTableInfo = "";
    public $Devices = [];
    public $device = "";
    public $DeviceInfo;

    public $Sensors;
    public $SensorTypes = [];
    public function LoadUserInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $this->user = $_SESSION["User"];
            }
            catch(Exception $e){
                $this->user = "";
            }
        }
    }
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
        }
        catch(Exception $e){

        }
    }
    public function LoadSensors(){
        try{
            $this->Sensors = DB::table("sensor")->get();      
        }
        catch(Exception $e){

        }
    }
    public function LoadSensorTypes(){
        try{
            $this->SensorTypes = DB::table("sensor_type")->get();      
        }
        catch(Exception $e){

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
    public function LoadDevices(){
        try{
            $this->Devices = DB::table("device")->where("organization_id", $this->OrgInfo->organization_id)->get();
        }
        catch(Exception $e){

        }
    }
    public function SetDefaultDevice(){
        try{
            $this->device = $this->Devices[0]->device_name;
            $this->DeviceInfo = $this->Devices[0];
        }
        catch(Exception $e){

        }
    }
    public function SetDevice($NewDeviceID){
        try{
            foreach ($this->Devices as $device){
                if ($device->device_eui == $NewDeviceID) {
                    $this->device = $device->device_name;
                    $this->DeviceInfo = $device;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function searchForSensor($SensorID){
        try{
            foreach( $this->Sensors as $sensor){
                if ($sensor->sensor_id == $SensorID) {
                    return $sensor;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function searchForSensorByName($SensorName){
        try{
            foreach( $this->Sensors as $sensor){
                if ($sensor->sensor_name == $SensorName) {
                    return $sensor;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function searchForSensorType($SensorTypeID){
        try{
            foreach( $this->SensorTypes as $sensorType){
                if ($sensorType->sensor_type_id == $SensorTypeID) {
                    return $sensorType;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $assocInfo = DB::table("device_sensor_association")->where("device_eui", $this->DeviceInfo->device_eui)->get();
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $Sensor = $this->searchForSensor($assoc->sensor_id);
                    $SensorType = $this->searchForSensorType($Sensor->sensor_type_id);
                    $this->DisplayTableInfo.=
                    "<tr id={$assoc->sensor_id}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->sensor_id}')\">
                        </td>
                        <td></td>
                        <td>{$this->DeviceInfo->device_name}</td>
                        <td>{$SensorType->sensor_type}</td>
                        <td>{$Sensor->sensor_name}</td>
                        <td>{$assoc->assoc_creation_time}</td>
                        <td>{$assoc->assoc_created_by}</td>
                        <td>{$assoc->assoc_desc}</td>
                    </tr>";
                }
            }
            catch(Exception $e){
                Log::channel("customlog")->error($e->getMessage());
            }
        }
    }
    public function SpaceToUnderScore($input){
        try{
            $input = str_replace(" ","_", $input);
            return $input;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SaveToDb($actions){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION["User"]))) { return null; }
        $ArrayOfActions = json_decode($actions, true);
        $Results = [];
        foreach ($ArrayOfActions as $action){
            $ActionSplit = explode("~!~", $action);
            $Query = $ActionSplit[0];
            $Value = $ActionSplit[1];
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                try{
                    $Sensor = $this->searchForSensorByName($Object->{"SENSOR NAME"});
                    $result = DB::table("device_sensor_association")->insert([
                        "device_eui"=> $this->DeviceInfo->device_eui,
                        "sensor_id"=> $Sensor->sensor_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>$_SESSION["User"]->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"associated device ".$this->DeviceInfo->device_eui." with sensor " . $Sensor->sensor_id
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, false);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("device_sensor_association")->where("device_eui", $this->DeviceInfo->device_eui)->whereIn("sensor_id", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"deleted device sensor association(s): ". $Value
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"update")){
                try{
                    $Object = json_decode($Value);
                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));

                    $Sensor = $this->searchForSensorByName($Object->{"SENSOR NAME"});
                    $result = DB::table("device_sensor_association")->where("device_eui", $this->DeviceInfo->device_eui)->where("sensor_id",$idToUpdate)->update([
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"updated device sensor association: ".$this->DeviceInfo->device_eui.", " . $Sensor->sensor_id
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
        }
        return $Results;
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
            "log_activity_desc"=>"Downloaded CSV of Device sensor association Info"
        ]);
    }
    public function render()
    {
        $this->LoadUserInfo();
        $this->LoadSensors();
        $this->LoadSensorTypes();
        return view('livewire..settings.device-sensor-association');
    }
}
