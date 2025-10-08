<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class PermissionInfo extends Component
{
        public $headers = [
        "SEQ.",
        "PERMISSION ID",
        "PERMISSION NAME",
        "COMPONENT ID",
        "RESOURCE NAME",
        "RESOURCE SUBNAME",
        "DESCRIPTION",
        "CAN CREATE",
        "CAN READ",
        "CAN UPDATE",
        "CAN DELETE",
        "CAN REPORT"
    ];
    public $DisplayTableInfo = "";
    public $Component;
    public $ComponentInfo;
    public $Components = [];

    public $Resource;
    public $resourcesInfo = [];
    public $Resources = [];
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
    public function LoadResources(){
        try{
            if (count((array)$this->ComponentInfo) == 0){
                return;
            }
            $this->Resources = Cache::get("resource", collect())
            ->where("component_id", $this->ComponentInfo->component_id)
            ->unique("resource_name")
            ->values()
            ->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultResource(){
        try{
            $this->Resource = $this->Resources[0]->resource_name;
            $this->resourcesInfo = Cache::get("resource", collect())
                    ->where("resource_name", $this->Resource)
                    ->values()
                    ->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setResource($resourceName){
        try{
            foreach($this->Resources as $Resource){
                if ($Resource->resource_name == $resourceName){
                    $this->Resource = $Resource->resource_name;
                    $this->resourcesInfo = Cache::get("resource", collect())
                            ->where("resource_name", $this->Resource)
                            ->values()
                            ->toArray();
                }
            }
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
            $this->LoadResources();
            $this->setDefaultResource();
            
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
                $RawTableInfo = collect(Cache::get("permission", collect()))
                    ->where("component_id", $this->ComponentInfo->component_id)
                    ->where("resource_name", $this->Resource);
                $this->DisplayTableInfo = "";
                foreach ($RawTableInfo as $key => $TableRow) {
                    $TRID = $this->SpaceToUnderScore($TableRow->permission_name);
                    $CanCreate = $TableRow->permission_create? 'true' : 'false';
                    $CanRead = $TableRow->permission_read? 'true' : 'false';
                    $CanUpdate = $TableRow->permission_update? 'true' : 'false';
                    $CanDelete = $TableRow->permission_delete? 'true' : 'false';
                    $CanReport = $TableRow->permission_report? 'true' : 'false';
                    $this->DisplayTableInfo.=
                    "<tr id='{$TRID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$TableRow->permission_name}')\">
                        </td>
                        <td></td>
                        <td>{$TableRow->permission_id}</td>
                        <td>{$TableRow->permission_name}</td>
                        <td>{$TableRow->component_id}</td>
                        <td>{$TableRow->resource_name}</td>
                        <td>{$TableRow->resource_sub_name}</td>
                        <td>{$TableRow->permission_desc}</td>
                        <td>{$CanCreate}</td>
                        <td>{$CanRead}</td>
                        <td>{$CanUpdate}</td>
                        <td>{$CanDelete}</td>
                        <td>{$CanReport}</td>
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
                if (strtolower($Object->{"PERMISSION ID"}) == "will generate automatically"){
                    $Object->{"PERMISSION ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("permission")->insert([
                        "permission_id" =>$Object->{"PERMISSION ID"},
                        "resource_name" => $this->Resource,
                        "resource_sub_name" => $Object->{"RESOURCE SUBNAME"},
                        "component_id" => $this->ComponentInfo->component_id,
                        "permission_name" => $Object->{"PERMISSION NAME"},
                        "permission_desc" => $Object->{"DESCRIPTION"},
                        "permission_create" => $Object->{"CAN CREATE"},
                        "permission_read" => $Object->{"CAN READ"},
                        "permission_update" => $Object->{"CAN UPDATE"},
                        "permission_delete" => $Object->{"CAN DELETE"},
                        "permission_report" => $Object->{"CAN REPORT"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted permission ". $Object->{"PERMISSION ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    dd($e);
                    array_push($Results, false);
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);

                try{
                    //permission name should be unique so deleteing just on that
                    $result = DB::table("permission")->whereIn("permission_name", $ItemsToDelete)->delete();
                    
                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                                "log_activity_desc"=>"Deleted permission from component ".$this->Component.": ". $Item
                            ]);
                        }
                    });
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    dd($e);
                    array_push($Results, 0);
                }
            }
            else if (str_contains(strtolower($Query),"update")){
                try{
                    $Object = json_decode($Value);

                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));
                    if (strtolower($Object->{"PERMISSION ID"}) == "will generate automatically"){
                        $Object->{"PERMISSION ID"} = DB::table("permission")->where("permission_name",$idToUpdate)->value("permission_id");
                    }
                    $result = DB::table("permission")->where("permission_name", $idToUpdate)->update([
                        "resource_sub_name" => $Object->{"RESOURCE SUBNAME"},
                        "permission_name" => $Object->{"PERMISSION NAME"},
                        "permission_desc" => $Object->{"DESCRIPTION"},
                        "permission_create" => $Object->{"CAN CREATE"},
                        "permission_read" => $Object->{"CAN READ"},
                        "permission_update" => $Object->{"CAN UPDATE"},
                        "permission_delete" => $Object->{"CAN DELETE"},
                        "permission_report" => $Object->{"CAN REPORT"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated permission ". $Object->{"PERMISSION ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("permission");
        Cache::rememberForever("permission", fn() => DB::table("permission")->get());
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
            "log_activity_desc"=>"Downloaded CSV of permission Info"
        ]);
    }
    public function render()
    {
        if (!(Cache::has("permission"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.permission-info');
    }
}
