<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
class RolePermissionAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "COMPONENT",
        "ROLE",
        "PERMISSION",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $component = "";
    public $ComponentInfo;
    public $Components = [];
    public $user = "";
    public $DisplayTableInfo = "";
    public $Roles = [];
    public $role = "";
    public $RoleInfo;
    public $Permissions = [];
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
    public function LoadSoftwareComponents(){
        try{
            $this->Components = Cache::get("software_component")->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultComponent(){
        try{
            $this->component = $this->Components[0]->component_name;
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
                    $this->component = $component->component_name;
                    $this->ComponentInfo = $component;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadRoles(){
        try{
            $this->Roles = Cache::get("role")->where("component_id", $this->ComponentInfo->component_id)->values()->toArray();
        }
        catch(Exception $e){

        }
    }
    public function SetDefaultRole(){
        try{
            $this->role = $this->Roles[0]->role_name;
            $this->RoleInfo = $this->Roles[0];
        }
        catch(Exception $e){

        }
    }
    public function SetRole($NewRoleID){
        try{
            foreach ($this->Roles as $Role){
                if ($Role->role_id == $NewRoleID) {
                    $this->role = $Role->role_name;
                    $this->RoleInfo = $Role;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function LoadPermission(){
        try{
            $this->Permissions = Cache::get("permission")
            ->where("component_id", $this->ComponentInfo->component_id)
            ->unique()
            ->values()
            ->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error("". $e->getMessage());
        }
    }
    public function SearchForPermission($PermissionID){
        try{
            foreach( $this->Permissions as $Permission ){
                if ($Permission->permission_id == $PermissionID) {
                    return $Permission;
                }
            }
        }
        catch(Exception $e){
        
        }
    }
    public function SearchForPermissionByName($PermissionName){
        try{
            foreach( $this->Permissions as $Permission ){
                if ($Permission->permission_name == $PermissionName) {
                    return $Permission;
                }
            }
        }
        catch(Exception $e){
        
        }
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $assocInfo = Cache::get("role_permission_association")->where("role_id", $this->RoleInfo->role_id);
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $Permission = $this->SearchForPermission($assoc->permission_id);
                    $this->DisplayTableInfo.=
                    "<tr id={$assoc->permission_id}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->permission_id}')\">
                        </td>
                        <td></td>
                        <td>{$this->ComponentInfo->component_name}</td>
                        <td>{$this->RoleInfo->role_name}</td>
                        <td>{$Permission->permission_name}</td>
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
                    $Permission = $this->SearchForPermissionByName($Object->{"PERMISSION"});
                    $result = DB::table("role_permission_association")->insert([
                        "role_id"=> $this->RoleInfo->role_id,
                        "permission_id"=> $Permission->permission_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>$_SESSION["User"]->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"associated permission ".$Permission->permission_id." with role " . $this->RoleInfo->role_id
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
                    $result = DB::table("role_permission_association")->where("role_id", $this->RoleInfo->role_id)->whereIn("permission_id", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"deleted role permission association(s): ". $Value
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

                    $Permission = $this->SearchForPermissionByName($Object->{"PERMISSION"});
                    $result = DB::table("role_permission_association")->where("role_id", $this->RoleInfo->role_id)->where("permission_id",$idToUpdate)->update([
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"updated role permission association: ".$this->RoleInfo->role_id.", " . $Permission->permission_id
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
        }
        Cache::forget("role_permission_association");
        Cache::rememberForever("role_permission_association", fn() => DB::table("role_permission_association")->get());
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
            "log_activity_desc"=>"Downloaded CSV of role permission association Info"
        ]);
    }
    public function render()
    {
        $this->LoadUserInfo();
        return view('livewire..settings.role-permission-association');
    }
}
