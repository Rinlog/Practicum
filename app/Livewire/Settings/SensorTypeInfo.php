<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
class SensorTypeInfo extends Component
{
    public $headers = [
        "SEQ.",
        "SENSOR TYPE ID",
        "SENSOR TYPE",
        "DESCRIPTION"
    ];
    public $SensorTypes = "";
    public function LoadSensorTypeInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $SensorTypeInfo = Cache::get("sensor_type")->all();
                $this->SensorTypes = "";
                foreach ($SensorTypeInfo as $key => $SensorType) {
                    $SensorTypeAsID = $this->SpaceToUnderScore($SensorType->sensor_type);
                    $this->SensorTypes.=
                    "<tr id='{$SensorTypeAsID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.SensorTypeChecked(\$event,'{$SensorType->sensor_type}')\">
                        </td>
                        <td></td>
                        <td>{$SensorType->sensor_type_id}</td>
                        <td>{$SensorType->sensor_type}</td>
                        <td>{$SensorType->sensor_type_desc}</td>
                    </tr>";
                }
            }
            catch(Exception $e){

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
                if (strtolower($Object->{"SENSOR TYPE ID"}) == "will generate automatically"){
                    $Object->{"SENSOR TYPE ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("sensor_type")->insert([
                        "sensor_type_id"=>$Object->{"SENSOR TYPE ID"},
                        "sensor_type" => $Object->{"SENSOR TYPE"},
                        "sensor_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted sensor type ". $Object->{"SENSOR TYPE"}
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
                    $result = DB::table("sensor_type")->whereIn("sensor_type", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted sensor type(s): ". $Value
                    ]);
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

                    $result = DB::table("sensor_type")->where("sensor_type", $idToUpdate)->update([
                        "sensor_type" => $Object->{"SENSOR TYPE"},
                        "sensor_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated sensor type ". $Object->{"SENSOR TYPE"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("sensor_type");
        Cache::rememberForever("sensor_type", fn() => DB::table("sensor_type")->get());
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
            "log_activity_desc"=>"Downloaded CSV of sensor type Info"
        ]);
    }
    public function render()
    {
        return view('livewire.settings.sensor-type-info');
    }
}
