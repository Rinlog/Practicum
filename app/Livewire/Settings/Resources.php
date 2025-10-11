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

    //used for crudr
    public $permissionSets = [
                            'Read-Update-Report'                => ['C'=>false,'R'=>true,'U'=>true,'D'=>false,'RE'=>true],
                            'Read'                              => ['C'=>false,'R'=>true,'U'=>false,'D'=>false,'RE'=>false],
                            'Read-Create'                       => ['C'=>true,'R'=>true,'U'=>false,'D'=>false,'RE'=>false],
                            'Read-Update'                       => ['C'=>false,'R'=>true,'U'=>true,'D'=>false,'RE'=>false],
                            'Read-Delete'                       => ['C'=>false,'R'=>true,'U'=>false,'D'=>true,'RE'=>false],
                            'Read-Report'                       => ['C'=>false,'R'=>true,'U'=>false,'D'=>false,'RE'=>true],
                            'Read-Create-Update'                => ['C'=>true,'R'=>true,'U'=>true,'D'=>false,'RE'=>false],
                            'Read-Create-Delete'                => ['C'=>true,'R'=>true,'U'=>false,'D'=>true,'RE'=>false],
                            'Read-Create-Report'                => ['C'=>true,'R'=>true,'U'=>false,'D'=>false,'RE'=>true],
                            'Read-Create-Update-Delete'         => ['C'=>true,'R'=>true,'U'=>true,'D'=>true,'RE'=>false],
                            'Read-Create-Update-Report'         => ['C'=>true,'R'=>true,'U'=>true,'D'=>false,'RE'=>true],
                            'Read-Create-Delete-Report'         => ['C'=>true,'R'=>true,'U'=>false,'D'=>true,'RE'=>true],
                            'Read-Update-Delete'                => ['C'=>false,'R'=>true,'U'=>true,'D'=>true,'RE'=>false],
                            'Read-Update-Delete-Report'         => ['C'=>false,'R'=>true,'U'=>true,'D'=>true,'RE'=>true],
                            'Read-Delete-Report'                => ['C'=>false,'R'=>true,'U'=>false,'D'=>true,'RE'=>true],
                            'Full-Access'                       => ['C'=>true,'R'=>true,'U'=>true,'D'=>true,'RE'=>true],
                        ];
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
        if (session()->get("User")) {
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
        if (!(session()->get("User"))) { return null; }
        $ArrayOfActions = json_decode($actions, true);
        $Results = [];
        foreach ($ArrayOfActions as $action){
            $ActionSplit = explode("~!~", $action);
            $Query = $ActionSplit[0];
            $Value = $ActionSplit[1];
            if (str_contains(strtolower($Query),"insert")) {
                $Crudr = $ActionSplit[2];
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
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Inserted resource ". $Object->{"RESOURCE NAME"} . ", " . $Object->{"RESOURCE SUBNAME"}
                    ]);
                    //doing after resource is inserted, if resource fails to insert this will also fail
                    if ($Crudr == "true"){
                        DB::transaction(function() use ($Object){
                            foreach ($this->permissionSets as $key=>$permission){
                                DB::table("permission")->insert([
                                    "permission_id"=>Uuid::uuid4(),
                                    "resource_name"=> trim($Object->{"RESOURCE NAME"}),
                                    "resource_sub_name"=> trim($Object->{"RESOURCE SUBNAME"}),
                                    "component_id" => $this->ComponentInfo->component_id,
                                    "permission_name"=>  $key." ". $Object->{"RESOURCE NAME"} . "_" . $Object->{"RESOURCE SUBNAME"},
                                    "permission_desc"=> "Automatically generated permission",
                                    "permission_create" => $permission['C'],
                                    "permission_read"=> $permission['R'],
                                    "permission_update"=>$permission['U'],
                                    "permission_delete"=>$permission['D'],
                                    "permission_report"=>$permission['RE']
                                ]);
                            }
                        },2);
                    }
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
                        DB::table("permission")->where("resource_name", $DoubleID[0])->where("resource_sub_name",$DoubleID[1])->where("component_id",$this->ComponentInfo->component_id)->delete();
                        $result = DB::table("resource")->where("resource_name", $DoubleID[0])->where("resource_sub_name", $DoubleID[1])->where("component_id",$this->ComponentInfo->component_id)->delete();
                    }

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
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
                    $Crudr = $ActionSplit[2];
                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));
                    $doubleID = explode("-!-",$idToUpdate);
                    //we will start by removing old permission info to allow for an update
                    DB::table("permission")->where("resource_name", $doubleID[0])->where("resource_sub_name",$doubleID[1])->where("component_id",$this->ComponentInfo->component_id)->delete();
                    $result = DB::table("resource")->where("resource_name", $doubleID[0])->where("resource_sub_name",$doubleID[1])->where("component_id",$this->ComponentInfo->component_id)->update([
                        "resource_name" =>$Object->{"RESOURCE NAME"},
                        "resource_sub_name" => $Object->{"RESOURCE SUBNAME"},
                        "resource_desc" => $Object->{"DESCRIPTION"}
                    ]);
                    if ($Crudr == "true"){
                        DB::transaction(function() use ($Object){
                            foreach ($this->permissionSets as $key=>$permission){
                                DB::table("permission")->insert([
                                    "permission_id"=>Uuid::uuid4(),
                                    "resource_name"=> trim($Object->{"RESOURCE NAME"}),
                                    "resource_sub_name"=> trim($Object->{"RESOURCE SUBNAME"}),
                                    "component_id" => $this->ComponentInfo->component_id,
                                    "permission_name"=>  $key." ". $Object->{"RESOURCE NAME"} . "_" . $Object->{"RESOURCE SUBNAME"},
                                    "permission_desc"=> "Automatically generated permission",
                                    "permission_create" => $permission['C'],
                                    "permission_read"=> $permission['R'],
                                    "permission_update"=>$permission['U'],
                                    "permission_delete"=>$permission['D'],
                                    "permission_report"=>$permission['RE']
                                ]);
                            }
                        },2);
                    }
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated resource ". $Object->{"RESOURCE NAME"} . ", " . $Object->{"RESOURCE SUBNAME"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    dd($e);
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("permission");
        Cache::forget("resource");
        Cache::rememberForever("resource", fn() => DB::table("resource")->get());
        Cache::rememberForever("resource", fn() => DB::table("permission")->get());
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
            "log_activity_desc"=>"Downloaded CSV of resource Info"
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
            $PermsDetailed = session()->get("settings-resource info");
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
        if (!(Cache::has("resource"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.resources');
    }
}
