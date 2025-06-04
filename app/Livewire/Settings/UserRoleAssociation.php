<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;
class UserRoleAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "USERNAME",
        "ROLE",
        "APPLICATION",
        "COMPONENT",
        "CREATION TIME",
        "CREATED BY",
        "EXPIRY DATE",
        "DESCRIPTION"
    ];
    public $component = "";
    public $ComponentInfo;
    public $Components = [];
    public $user = "";
    public $DisplayTableInfo = "";
    public $application = "";
    public $Applications = [];
    public $ApplicationInfo;

    public $Roles = [];
    public $Organizations = [];
    public $Users = [];
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
            $this->Components = DB::table("software_component")->get()->toArray();
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
    public function LoadApplications(){
        try{
            $applications = DB::table("application")->get();
            $this->Applications = $applications->toArray();
        }
        catch(Exception $e){

        }
    }
    public function setDefaultApplication(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
               
                $this->application = $this->Applications[0]->application_name;
                $this->ApplicationInfo = $this->Applications[0];
            }
            catch(Exception $e){
                $this->application = "";
            }
        }
    }
    public function SetApplication($NewOrgID){
        foreach ($this->Applications as $Application){
            if ($Application->application_id == $NewOrgID) {
                $this->ApplicationInfo = $Application;
                $this->application = $Application->application_name;
            }
        }
    }
    public function LoadOrganizations(){
        try{
            $organizations = DB::table("organization")->get();
            $this->Organizations = $organizations->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadUsers(){
        try{
            $this->Users = DB::table("users")->get()->toArray();
        }
        catch(Exception $e){

        }
    }
    //based on components so load components first before roles
    public function LoadRoles(){
        try{
            $this->Roles = DB::table("role")->where("component_id", $this->ComponentInfo->component_id)->get()->toArray();
        }
        catch(Exception $e){

        }
    }
    public function SearchForUser($userID){
        try{
            foreach ($this->Users as $User){
                if ($User->user_id == $userID){
                    return $User;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForUserByName($userName){
        try{
            foreach ($this->Users as $User){
                if ($User->user_username == $userName){
                    return $User;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForRole($roleID){
        try{
            foreach ($this->Roles as $Role){
                if ($Role->role_id == $roleID){
                    return $Role;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForRoleByName($roleName){
        try{
            foreach ($this->Roles as $Role){
                if ($Role->role_name == $roleName){
                    return $Role;
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
                $assocInfo = DB::table("user_role_association")->where("application_id", $this->ApplicationInfo->application_id)->get();
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $User = $this->SearchForUser($assoc->user_id);
                    $Role = $this->SearchForRole($assoc->role_id);
                    $TRID = $this->SpaceToUnderScore($assoc->user_id) . "_" . $this->SpaceToUnderScore($assoc->role_id);
                    $RawID = $assoc->user_id . "-!-" . $assoc->role_id;
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$RawID}')\">
                        </td>
                        <td></td>
                        <td>{$User->user_username}</td>
                        <td>{$Role->role_name}</td>
                        <td>{$this->application}</td>
                        <td>{$this->component}</td>
                        <td>{$assoc->assoc_creation_time}</td>
                        <td>{$assoc->assoc_created_by}</td>
                        <td>{$assoc->assoc_expiry_date}</td>
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
                    $User = $this->SearchForUserByName($Object->{"USERNAME"});
                    $Role = $this->SearchForRoleByName($Object->{"ROLE"});
                    $result = DB::table("user_role_association")->insert([
                        "user_id"=> $User->user_id,
                        "role_id"=> $Role->role_id,
                        "application_id"=> $this->ApplicationInfo->application_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>$_SESSION["User"]->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                        "assoc_expiry_date"=> $Object->{"EXPIRY DATE"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Inserted user role association: " . $User->user_id . ", " . $Role->role_id
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
                    foreach($ItemsToDelete as $Item){
                        $DoubleID = explode("-!-",$Item);
                        $result = DB::table("user_role_association")->where("application_id", $this->ApplicationInfo->application_id)->where("user_id", $DoubleID[0])->where("role_id", $DoubleID[1])->delete();
                    }

                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Removed user role Association(s): " . $Value
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

                    $User = $this->SearchForUserByName($Object->{"USERNAME"});
                    $Role = $this->SearchForRoleByName($Object->{"ROLE"});
                    
                    $DoubleID = explode("-!-",$idToUpdate);
                    $result = DB::table("user_role_association")->where("application_id", $this->ApplicationInfo->application_id)->where("user_id",$DoubleID[0])->where("role_id",$DoubleID[1])->update([
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                        "assoc_expiry_date"=> $Object->{"EXPIRY DATE"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Updated user role association " . $User->user_id . ", " . $Role->role_id
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
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
            "log_activity_desc"=>"Downloaded CSV of user role association Info"
        ]);
    }
    public function render()
    {
        $this->LoadUserInfo();
        $this->LoadOrganizations();
        $this->LoadUsers();
        return view('livewire..settings.user-role-association');
    }
}
