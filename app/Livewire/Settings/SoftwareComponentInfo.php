<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Cache;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class SoftwareComponentInfo extends Component
{
     public $headers = [
        "SEQ.",
        "SOFTWARE COMPONENT ID",
        "SOFTWARE COMPONENT NAME",
        "HAS API",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";

    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $ComponentInfo = Cache::get("software_component", collect());
                $this->DisplayTableInfo = "";
                foreach ($ComponentInfo as $key => $Component) {
                    $TRID = $this->SpaceToUnderScore($Component->component_name);
                    $hasAPI = $Component->component_has_api ? 'true' : 'false';
                    $this->DisplayTableInfo.=
                    "<tr id='{$TRID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$Component->component_name}')\">
                        </td>
                        <td></td>
                        <td>{$Component->component_id}</td>
                        <td>{$Component->component_name}</td>
                        <td>{$hasAPI}</td>
                        <td>{$Component->component_desc}</td>
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
                if (strtolower($Object->{"SOFTWARE COMPONENT ID"}) == "will generate automatically"){
                    $Object->{"SOFTWARE COMPONENT ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("software_component")->insert([
                        "component_id"=>$Object->{"SOFTWARE COMPONENT ID"},
                        "component_name" => $Object->{"SOFTWARE COMPONENT NAME"},
                        "component_has_api"=>$Object->{"HAS API"},
                        "component_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted component ". $Object->{"SOFTWARE COMPONENT ID"}
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
                    $result = DB::table("software_component")->whereIn("component_name", $ItemsToDelete)->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                                "log_activity_desc"=>"Deleted component ". $Item
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

                    if (strtolower($Object->{"SOFTWARE COMPONENT ID"}) == "will generate automatically"){
                        $Object->{"SOFTWARE COMPONENT ID"} = DB::table("software_component")->where("component_name", $idToUpdate)->value("component_id");
                    }
                    $result = DB::table("software_component")->where("component_name", $idToUpdate)->update([
                        "component_name" => $Object->{"SOFTWARE COMPONENT NAME"},
                        "component_has_api"=>$Object->{"HAS API"},
                        "component_desc"=>$Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated component ". $Object->{"SOFTWARE COMPONENT ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("software_component");
        Cache::rememberForever("software_component", fn() => DB::table("software_component")->get());
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
            $PermsDetailed = session()->get("settings-software component info");
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
        if (!(Cache::has("software_component"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.software-component-info');
    }
}
