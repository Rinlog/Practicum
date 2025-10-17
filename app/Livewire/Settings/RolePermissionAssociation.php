<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
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
        if (session()->get("User")) {
            try{
                $this->user = session()->get("User");
            }
            catch(Exception $e){
                $this->user = "";
            }
        }
    }
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
            $this->LoadRoles();
            $this->SetDefaultRole();
            $this->LoadPermission();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadRoles(){
        try{
            $this->Roles = Cache::get("role", collect())->where("component_id", $this->ComponentInfo->component_id)->values()->toArray();
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
            $this->Permissions = Cache::get("permission", collect())
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
    public function RegenPageCache(){
        Cache::forget("role_permission_association");
        Cache::rememberForever("role_permission_association", fn() => DB::table("role_permission_association")->get());
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
        if (session()->get("User")) {
            try{
                $assocInfo = Cache::get("role_permission_association", collect())->where("role_id", $this->RoleInfo->role_id);
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
        if (!(session()->get("User"))) { return null; }
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
                        "assoc_created_by"=>session()->get("User")->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
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

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted role permission association ". $Item
                            ]);
                        }
                    });
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
                        "log_activity_performed_by"=> session()->get("User")->user_username,
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
        $this->RegenPageCache();
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
            "log_activity_desc"=>"Downloaded CSV of role permission association Info"
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
            $PermsDetailed = session()->get("settings-role-permission association");
            if (session()->get("IsSuperAdmin") == true){
                $this->Perms['create'] = true;
                $this->Perms['delete'] = true;
                $this->Perms["read"] = true;
                $this->Perms['update'] = true;
                $this->Perms['report'] = true;
            }
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
        if (!(Cache::has("role_permission_association"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        return view('livewire..settings.role-permission-association');
    }
}
