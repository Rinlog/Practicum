<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;

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
        if (isset($_SESSION["User"])) {
            try{
                $this->user = $_SESSION["User"];
            }
            catch(Exception $e){
                $this->user = "";
            }
        }
    }
    public function LoadSensors(){
        try{
            $this->Sensors = DB::table("sensor")->get()->toArray();
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
            $this->SensorDataTypes = DB::table("sensor_data_types")->get()->toArray();
            $this->SensorDataTypeNames = DB::table("sensor_data_types")->groupBy("data_type")->get("data_type")->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $assocInfo = DB::table("sensor_data_types_association")->where("sensor_id", $this->SensorInfo->sensor_id)->get();
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $DataValueSet = $this->DecodePostGresJson($assoc->data_value_set);
                    $this->DisplayTableInfo.=
                    "<tr id={$assoc->data_item_name}>
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
        if (!(isset($_SESSION["User"]))) { return null; }
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
                        "assoc_created_by"=>$_SESSION["User"]->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
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
                    
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted sensor data type association(s): " . $Value
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
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
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
            "log_activity_desc"=>"Downloaded CSV of user role association Info"
        ]);
    }
    public function render()
    {
        $this->LoadUserInfo();
        $this->LoadSensorDataTypes();
        return view('livewire..settings.sensor-data-type-association');
    }
}
