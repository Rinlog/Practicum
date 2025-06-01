<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \Exception;
class SensorDataTypes extends Component
{
    public $headers = [
        "SEQ.",
        "SENSOR DATA TYPE",
        "DATA VALUE SET TYPE",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";

    public $ComboBoxOptions = [];
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $RawTableInfo = DB::table("sensor_data_types")->get();
                $this->DisplayTableInfo = "";
                foreach ($RawTableInfo as $key => $TableRow) {
                    $TRID = $this->SpaceToUnderScore($TableRow->data_type) . "_" . $this->SpaceToUnderScore($TableRow->data_value_set_type);
                    $RawID = $TableRow->data_type . "-!-" . $TableRow->data_value_set_type;
                    $this->DisplayTableInfo.=
                    "<tr id='{$TRID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$RawID}')\">
                        </td>
                        <td></td>
                        <td>{$TableRow->data_type}</td>
                        <td>{$TableRow->data_value_set_type}</td>
                        <td>{$TableRow->data_type_desc}</td>
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
                try{
                    $result = DB::table("sensor_data_types")->insert([
                        "data_type"=>$Object->{"SENSOR DATA TYPE"},
                        "data_value_set_type" => $Object->{"DATA VALUE SET TYPE"},
                        "data_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted sensor data type ". $Object->{"SENSOR DATA TYPE"} . ", " . $Object->{"DATA VALUE SET TYPE"}
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
                    foreach ($ItemsToDelete as $ItemToDelete){
                        $DoubleID = explode("-!-", $ItemToDelete);
                        $result = DB::table("sensor_data_types")->where("data_type", $DoubleID[0])->where("data_value_set_type", $DoubleID[1])->delete();
                    }

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted sensor data type(s): ". $Value
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
                    $doubleID = explode("-!-",$idToUpdate);
                    $result = DB::table("sensor_data_types")->where("data_type", $doubleID[0])->where("data_value_set_type",$doubleID[1])->update([
                        "data_type"=>$Object->{"SENSOR DATA TYPE"},
                        "data_value_set_type" => $Object->{"DATA VALUE SET TYPE"},
                        "data_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated sensor data type ". $Object->{"SENSOR DATA TYPE"} . ", " . $Object->{"DATA VALUE SET TYPE"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
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
            "log_activity_desc"=>"Downloaded CSV of sensor data type Info"
        ]);
    }

    public function render()
    {
        $this->ComboBoxOptions = [(object)["DataType"=>"Enumeration"],(object)["DataType"=>"Range"],(object)["DataType"=>"Other"]];
        return view('livewire..settings.sensor-data-types');
    }
}
