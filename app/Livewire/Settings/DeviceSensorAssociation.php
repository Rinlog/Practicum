<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;

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
        if (session()->get("User")) {
            try{
                $this->user = session()->get("User");
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
        if (session()->get("User")) {
            try{
                $organizationInfo = collect(Cache::get('organization', collect()))
                    ->firstWhere("organization_id", session()->get("User")->organization_id);
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
            $this->Organizations = Cache::get("organization",collect())->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadSensors(){
        try{
            $this->Sensors = Cache::get("sensor", collect())->values()->toArray(); 
        }
        catch(Exception $e){

        }
    }
    public function LoadSensorTypes(){
        try{
            $this->SensorTypes = Cache::get("sensor_type", collect())->values()->toArray();      
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
        $this->LoadDevices();
        $this->SetDefaultDevice();
    }
    public function LoadDevices(){
        try{
            $this->Devices = Cache::get("device", collect())->where("organization_id", $this->OrgInfo->organization_id)->values()->toArray();
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
    public function RegenPageCache(){
        Cache::forget("device_sensor_association");
        Cache::rememberForever("device_sensor_association", fn() => DB::table("device_sensor_association")->get());
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $assocInfo = Cache::get("device_sensor_association", collect())->where("device_eui", $this->DeviceInfo->device_eui);
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
        if (!(session()->get("User"))) { return null; }
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
                        "assoc_created_by"=>session()->get("User")->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
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

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted device sensor association ". $this->DeviceInfo->device_eui . "-" . $Item
                            ]);
                        }
                    });
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
                        "log_activity_performed_by"=> session()->get("User")->user_username,
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
        $this->RegenPageCache();
        return $Results;
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
            "log_activity_desc"=>"Downloaded CSV of Device sensor association Info"
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
            $PermsDetailed = session()->get("settings-device-sensor association");
            if (session()->get("IsSuperAdmin") == true){
                $this->Perms['create'] = true;
                $this->Perms['delete'] = true;
                $this->Perms["read"] = true;
                $this->Perms['update'] = true;
                $this->Perms['report'] = true;
            }
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
        if (!(Cache::has("device_sensor_association"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        $this->LoadSensors();
        $this->LoadSensorTypes();
        return view('livewire.settings.device-sensor-association');
    }
}
