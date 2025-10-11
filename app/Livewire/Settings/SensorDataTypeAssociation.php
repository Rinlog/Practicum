<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class SensorDataTypeAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "SENSOR ID",
        "DATA ITEM NAME",
        "INCOMING DATA ITEM NAME",
        "DATA TYPE",
        "DATA VALUE SET TYPE",
        "DATA VALUE SET",
        "DATA UNITS",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $sensor = "";
    public $SensorInfo;
    public $Sensors = [];
    public $user = "";
    public $SensorDataTypes = [];
    public $SensorDataTypeNames = [];
    public $DisplayTableInfo = "";
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
    public function LoadSensors(){
        try{
            $this->Sensors = Cache::get("sensor", collect())->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultSensor(){
        try{
            $this->sensor = $this->Sensors[0]->sensor_name;
            $this->SensorInfo = $this->Sensors[0];
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setSensor($SensorID){
        try{
           foreach($this->Sensors as $sensor){
                if($sensor->sensor_id == $SensorID){
                    $this->sensor = $sensor->sensor_name;
                    $this->SensorInfo = $sensor;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadSensorDataTypes(){
        try{
            $this->SensorDataTypes = Cache::get("sensor_data_types", collect())->values()->toArray();
            $this->SensorDataTypeNames = Cache::get("sensor_data_types", collect())->pluck("data_type")->unique()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $assocInfo = Cache::get("sensor_data_types_association", collect())->where("sensor_id", $this->SensorInfo->sensor_id);
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $DataValueSet = $this->DecodePostGresJson($assoc->data_value_set);
                    $TRID = $this->SpaceToUnderScore($assoc->data_item_name);
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->data_item_name}')\">
                        </td>
                        <td></td>
                        <td>{$assoc->sensor_id}</td>
                        <td>{$assoc->data_item_name}</td>
                        <td>{$assoc->incoming_data_item_name}</td>
                        <td>{$assoc->data_type}</td>
                        <td>{$assoc->data_value_set_type}</td>
                        <td>{$DataValueSet}</td>
                        <td>{$assoc->data_units}</td>
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
    function FormatToPostGresJson($input){
        try{
            $input = json_encode([$input]);
            $input = str_replace("[","{", $input);
            $input = str_replace("]","}", $input);
            return $input;
        }
        catch(Exception $e){
            Log::channel("customLog")->error($e->getMessage());
        }
    }
    function DecodePostGresJson($input){
        try{
            $input = str_replace("{","[", $input);
            $input = str_replace("}","]", $input);
            $input = json_decode($input);
            return $input[0];
        }
        catch(Exception $e){
            Log::channel("customLog")->error($e->getMessage());
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
                $ValueSet = $this->FormatToPostGresJson($Object->{"DATA VALUE SET"});
                try{
                    $result = DB::table("sensor_data_types_association")->insert([
                        "sensor_id"=> $Object->{"SENSOR ID"},
                        "data_item_name"=> $Object->{"DATA ITEM NAME"},
                        "incoming_data_item_name"=> $Object->{"INCOMING DATA ITEM NAME"},
                        "data_type"=> $Object->{"DATA TYPE"},
                        "data_value_set_type"=> $Object->{"DATA VALUE SET TYPE"},
                        "data_value_set"=> $ValueSet,
                        "data_units"=> $Object->{"DATA UNITS"},
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>session()->get("User")->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Inserted sensor data type association: " . $Object->{"SENSOR ID"} . ", " . $Object->{"DATA ITEM NAME"}
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

                    $result = DB::table("sensor_data_types_association")->where("sensor_id", $this->SensorInfo->sensor_id)->whereIn("data_item_name", $ItemsToDelete)->delete();
                    
                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted sensor data type association ". $Item
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

                    $ValueSet = $this->FormatToPostGresJson($Object->{"DATA VALUE SET"});
                    $result = DB::table("sensor_data_types_association")->where("sensor_id", $this->SensorInfo->sensor_id)->where("data_item_name", $idToUpdate)->update([
                        "data_item_name"=> $Object->{"DATA ITEM NAME"},
                        "incoming_data_item_name"=> $Object->{"INCOMING DATA ITEM NAME"},
                        "data_type"=> $Object->{"DATA TYPE"},
                        "data_value_set_type"=> $Object->{"DATA VALUE SET TYPE"},
                        "data_value_set"=> $ValueSet,
                        "data_units"=> $Object->{"DATA UNITS"},
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                   DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated sensor data type association: " . $Object->{"SENSOR ID"} . ", " . $Object->{"DATA TYPE"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
        }
        Cache::forget("sensor_data_types_association");
        Cache::rememberForever("sensor_data_types_association", fn() => DB::table("sensor_data_types_association")->get());
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
            "log_activity_desc"=>"Downloaded CSV of user role association Info"
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
            $PermsDetailed = session()->get("settings-sensor-data types association");
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
        if (!(Cache::has("sensor_data_types_association"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        $this->LoadSensorDataTypes();
        return view('livewire..settings.sensor-data-type-association');
    }
}
