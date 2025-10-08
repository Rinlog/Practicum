<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class DeviceTypeInfo extends Component
{
    public $headers = [
        "SEQ.",
        "DEVICE TYPE ID",
        "DEVICE TYPE",
        "DESCRIPTION"
    ];
    public $DeviceTypes = "";
    public function LoadDeviceTypeInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $DeviceTypeInfo = Cache::get("device_type", collect())->all();
                $this->DeviceTypes = "";
                foreach ($DeviceTypeInfo as $key => $DeviceType) {
                    $DeviceTypeAsID = $this->SpaceToUnderScore($DeviceType->device_type);
                    $this->DeviceTypes.=
                    "<tr id='{$DeviceTypeAsID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.DeviceTypeChecked(\$event,'{$DeviceType->device_type}')\">
                        </td>
                        <td></td>
                        <td>{$DeviceType->device_type_id}</td>
                        <td>{$DeviceType->device_type}</td>
                        <td>{$DeviceType->device_type_desc}</td>
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
                if (strtolower($Object->{"DEVICE TYPE ID"}) == "will generate automatically"){
                    $Object->{"DEVICE TYPE ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("device_type")->insert([
                        "device_type_id"=>$Object->{"DEVICE TYPE ID"},
                        "device_type" => $Object->{"DEVICE TYPE"},
                        "device_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted device type ". $Object->{"DEVICE TYPE"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    Log::channel("customlog")->info($e);
                    array_push($Results, false);
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("device_type")->whereIn("device_type", $ItemsToDelete)->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                                "log_activity_desc"=>"Deleted device type ". $Item
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

                    $result = DB::table("device_type")->where("device_type", $idToUpdate)->update([
                        "device_type" => $Object->{"DEVICE TYPE"},
                        "device_type_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated device type ". $Object->{"DEVICE TYPE"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("device_type");
        Cache::rememberForever("device_type", fn() => DB::table("device_type")->get());
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
            "log_activity_desc"=>"Downloaded CSV of device type Info"
        ]);
    }
    public function render()
    {
        if (!(Cache::has("device_type"))){
            Artisan::call("precache:tables");
        }
        return view('livewire.settings.device-type-info');
    }
}
