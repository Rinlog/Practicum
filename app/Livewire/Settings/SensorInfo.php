<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class SensorInfo extends Component
{
     public $headers = [
        "SEQ.",
        "SENSOR ID",
        "SENSOR TYPE",
        "SENSOR NAME",
        "DESCRIPTION"
    ];
    public $Sensors = "";

    public $SensorTypeInfo = [];

    public function LoadSensorTypeInfo(){
        try{
            $this->SensorTypeInfo = Cache::get("sensor_type", collect())->all("sensor_type_id","sensor_type_name");
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForSensorType(string $typeID){
        try{
            foreach($this->SensorTypeInfo as $key => $value){
                if ($value->{"sensor_type_id"} == $typeID){
                    return $value;
                }
            }
            return null;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForSensorTypeByName(string $Name){
        try{
            foreach($this->SensorTypeInfo as $key => $value){
                if ($value->{"sensor_type"} == $Name){
                    return $value;
                }
            }
            return null;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadSensorInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $SensorInfo = Cache::get("sensor", collect());
                $this->Sensors = "";
                foreach ($SensorInfo as $key => $Sensor) {
                    $SensorType = $this->SearchForSensorType($Sensor->sensor_type_id);
                    $SensorNameAsID = $this->SpaceToUnderScore($Sensor->sensor_name);
                    $this->Sensors.=
                    "<tr id='{$SensorNameAsID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.SensorChecked(\$event,'{$Sensor->sensor_name}')\">
                        </td>
                        <td></td>
                        <td>{$Sensor->sensor_id}</td>
                        <td>{$SensorType->sensor_type}</td>
                        <td>{$Sensor->sensor_name}</td>
                        <td>{$Sensor->sensor_desc}</td>
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
                if (strtolower($Object->{"SENSOR ID"}) == "will generate automatically"){
                    $Object->{"SENSOR ID"} = Uuid::uuid4()->toString();
                }
                $SensorType = $this->SearchForSensorTypeByName($Object->{"SENSOR TYPE"});
                try{
                    $result = DB::table("sensor")->insert([
                        "sensor_id"=>$Object->{"SENSOR ID"},
                        "sensor_type_id" => $SensorType->{"sensor_type_id"},
                        "sensor_name"=>$Object->{"SENSOR NAME"},
                        "sensor_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Inserted sensor ". $Object->{"SENSOR ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, false);
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("sensor")->whereIn("sensor_name", $ItemsToDelete)->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted sensor ". $Item
                            ]);
                        }
                    });
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
            else if (str_contains(strtolower($Query),"update")){
                try{
                    $Object = json_decode($Value);
                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));

                    $SensorType = $this->SearchForSensorTypeByName($Object->{"SENSOR TYPE"});
                    $result = DB::table("sensor")->where("sensor_name", $idToUpdate)->update([
                        "sensor_type_id" => $SensorType->{"sensor_type_id"},
                        "sensor_name"=>$Object->{"SENSOR NAME"},
                        "sensor_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated sensor ". $SensorType->{"sensor_type_id"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("sensor");
        Cache::rememberForever("sensor", fn() => DB::table("sensor")->get());
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
            "log_activity_desc"=>"Downloaded CSV of sensor Info"
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
            $PermsDetailed = session()->get("settings-sensor info");
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
        if (!(Cache::has("sensor"))){
            Artisan::call("precache:tables");
        }
        return view('livewire.settings.sensor-info');
    }
}
