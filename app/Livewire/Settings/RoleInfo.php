<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class RoleInfo extends Component
{
    public $headers = [
        "SEQ.",
        "COMPONENT ID",
        "ROLE ID",
        "ROLE NAME",
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
        if (session()->get("User")) {
            try{
                $RawTableInfo = Cache::get("role", collect())
                ->where("component_id", $this->ComponentInfo->component_id);
                $this->DisplayTableInfo = "";
                foreach ($RawTableInfo as $key => $TableRow) {
                    $TRID = $this->SpaceToUnderScore($TableRow->role_name);
                    $this->DisplayTableInfo.=
                    "<tr id='{$TRID}'>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$TableRow->role_name}')\">
                        </td>
                        <td></td>
                        <td>{$TableRow->component_id}</td>
                        <td>{$TableRow->role_id}</td>
                        <td>{$TableRow->role_name}</td>
                        <td>{$TableRow->role_desc}</td>
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
                $Object = json_decode($Value);
                if (strtolower($Object->{"ROLE ID"}) == "will generate automatically"){
                    $Object->{"ROLE ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("role")->insert([
                        "role_id" =>$Object->{"ROLE ID"},
                        "component_id" => $this->ComponentInfo->component_id,
                        "role_name" => $Object->{"ROLE NAME"},
                        "role_desc" => $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Inserted role ". $Object->{"ROLE ID"}
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
                    //role name should be unique so deleteing just on that
                    $result = DB::table("role")->whereIn("role_name", $ItemsToDelete)->delete();
                    
                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted role from component ".$this->Component.": ". $Item
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
                    if (strtolower($Object->{"ROLE ID"}) == "will generate automatically"){
                        $Object->{"ROLE ID"} = DB::table("role")->where("role_name",$idToUpdate)->value("role_id");
                    }
                    $result = DB::table("role")->where("role_name", $idToUpdate)->update([
                        "role_name" => $Object->{"ROLE NAME"},
                        "role_desc" => $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated role ". $Object->{"ROLE ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("role");
        Cache::rememberForever("role", fn() => DB::table("role")->get());
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
            "log_activity_desc"=>"Downloaded CSV of permission Info"
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
            $PermsDetailed = session()->get("settings-role info");
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
        if (!(Cache::has("role"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.role-info');
    }
}
