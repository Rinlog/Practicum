<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class Resources extends Component
{
    public $headers = [
        "SEQ.",
        "SOFTWARE COMPONENT ID",
        "RESOURCE NAME",
        "RESOURCE SUBNAME",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";
    public $Component;
    public $ComponentInfo;
    public $Components = [];
    public function LoadSoftwareComponents(){
        try{
            $this->Components = Cache::get("software_component", collect())->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultComponent(){
        try{
            $this->Component = $this->Components[0]->component_name;
            $this->ComponentInfo = $this->Components[0];
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setComponent($componentID){
        try{
           foreach($this->Components as $component){
                if($component->component_id == $componentID){
                    $this->Component = $component->component_name;
                    $this->ComponentInfo = $component;
                }
            }
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
                $RawTableInfo = Cache::get("resource", collect())
                ->where("component_id", $this->ComponentInfo->component_id);
                $this->DisplayTableInfo = "";
                foreach ($RawTableInfo as $key => $TableRow) {
                    $TRID = $this->SpaceToUnderScore($TableRow->resource_name) . "_" . $this->SpaceToUnderScore($TableRow->resource_sub_name);
                    $RawID = $TableRow->resource_name . "-!-" . $TableRow->resource_sub_name;
                    $this->DisplayTableInfo.=
                    "<tr id='{$TRID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$RawID}')\">
                        </td>
                        <td></td>
                        <td>{$TableRow->component_id}</td>
                        <td>{$TableRow->resource_name}</td>
                        <td>{$TableRow->resource_sub_name}</td>
                        <td>{$TableRow->resource_desc}</td>
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
                    $result = DB::table("resource")->insert([
                        "resource_name" =>$Object->{"RESOURCE NAME"},
                        "resource_sub_name" => $Object->{"RESOURCE SUBNAME"},
                        "component_id" =>$this->ComponentInfo->component_id,
                        "resource_desc" => $Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted resource ". $Object->{"RESOURCE NAME"} . ", " . $Object->{"RESOURCE SUBNAME"}
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
                        $result = DB::table("resource")->where("resource_name", $DoubleID[0])->where("resource_sub_name", $DoubleID[1])->where("component_id",$this->ComponentInfo->component_id)->delete();
                    }

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted resources(s) from component ".$this->Component.": ". $Value
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
                    $result = DB::table("resource")->where("resource_name", $doubleID[0])->where("resource_sub_name",$doubleID[1])->where("component_id",$this->ComponentInfo->component_id)->update([
                        "resource_name" =>$Object->{"RESOURCE NAME"},
                        "resource_sub_name" => $Object->{"RESOURCE SUBNAME"},
                        "resource_desc" => $Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated resource ". $Object->{"RESOURCE NAME"} . ", " . $Object->{"RESOURCE SUBNAME"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("resource");
        Cache::rememberForever("resource", fn() => DB::table("resource")->get());
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
            "log_activity_desc"=>"Downloaded CSV of resource Info"
        ]);
    }
    public function render()
    {
        if (!(Cache::has("resource"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.resources');
    }
}
