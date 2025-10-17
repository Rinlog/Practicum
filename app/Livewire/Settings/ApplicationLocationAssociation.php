<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Artisan;
use \Exception;

class ApplicationLocationAssociation extends Component
{
        public $headers = [
        "SEQ.",
        "APPLICATION",
        "LOCATION",
        "SUB-LOCATION",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $application = "";
    public $Applications = [];
    public $ApplicationInfo;
    public $user = "";
    public $DisplayTableInfo = "";
    public $Organizations;
    public $Locations;
    public $SubLocations;
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
    public function LoadOrganizations(){
        try{
            $organizations = Cache::get("organization", collect());
            $this->Organizations = $organizations->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadLocations(){
        try{
            $this->Locations = Cache::get("location", collect());
        }
        catch(Exception $e){

        }
    }
    public function LoadSubLocations(){
        try{
            $this->SubLocations = Cache::get("sub_location", collect());
        }
        catch(Exception $e){

        }
    }
    public function RegenPageCache(){
        Cache::forget("application_location_association");
        Cache::rememberForever("application_location_association", fn() => DB::table("application_location_association")->get());
    }
    public function LoadApplications(){
        try{
            if (session()->get("IsSuperAdmin") == true){
                $this->Applications = Cache::get("application", collect())->values()->toArray();
            }
            else{
                $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", session("User")->user_id);

                $ApplicationsArray = $userRoles->pluck("application_id")->all();
                if (count($ApplicationsArray) > 0){
                    $this->Applications = Cache::get("application", collect())
                        ->whereIn("application_id", $ApplicationsArray);
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultApplication(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
               
                $this->application = $this->Applications[0]->application_name;
                $this->ApplicationInfo = $this->Applications[0];
            }
            catch(Exception $e){
                $this->application = "";
            }
        }
    }
    public function SearchForLocation($LocationID){
        try{
            foreach ($this->Locations as $Location){
                if ($Location->location_id == $LocationID) {
                    return $Location;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForSubLocation($LocationID){
        try{
            foreach ($this->SubLocations as $Location){
                if ($Location->sub_location_id == $LocationID) {
                    return $Location;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForSubLocationByName($SubLocationName){
        try{
            foreach ($this->SubLocations as $Location){
                if ($Location->sub_location_name == $SubLocationName) {
                    return $Location;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForLocationByName($LocationName){
        try{
            foreach ($this->Locations as $Location){
                if ($Location->location_name == $LocationName) {
                    return $Location;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
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
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $assocInfo = Cache::get("application_location_association", collect())
                ->where("application_id", $this->ApplicationInfo->application_id);
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $Location = $this->SearchForLocation($assoc->location_id);
                    $SubLocation = $this->SearchForSubLocation($assoc->sub_location_id);
                    $this->DisplayTableInfo.=
                    "<tr id={$assoc->sub_location_id}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->sub_location_id}')\">
                        </td>
                        <td></td>
                        <td>{$this->application}</td>
                        <td>{$Location->location_name}</td>
                        <td>{$SubLocation->sub_location_name}</td>
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
    public function GetApplicationIDsFromNames($names){
        try{
            $result = Cache::get("application", collect())->wherein("application_name",$names);
            $ArrayOfIDs = [];
            foreach ($result as $key => $value) {
                array_push($ArrayOfIDs, $value->application_id);
            }
            return $ArrayOfIDs;
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
                    $Location = $this->SearchForLocationByName($Object->{"LOCATION"});
                    $SubLocation = $this->SearchForSubLocationByName($Object->{"SUB-LOCATION"});
                    $result = DB::table("application_location_association")->insert([
                        "application_id"=> $this->ApplicationInfo->application_id,
                        "sub_location_id"=> $SubLocation->sub_location_id,
                        "location_id"=> $Location->location_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>session()->get("User")->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> session()->get("User")->user_username,
                        "applog_activity_desc"=>"associated location ".$Location->location_id." with application " . $this->application
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
                    $result = DB::table("application_location_association")->where("application_id", $this->ApplicationInfo->application_id)->whereIn("sub_location_id", $ItemsToDelete)->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                           DB::table("application_log")->insert([
                                "application_id" => $this->ApplicationInfo->application_id,
                                "applog_activity_time"=>now(),
                                "applog_activity_type"=>"DELETE",
                                "applog_activity_performed_by"=> session()->get("User")->user_username,
                                "applog_activity_desc"=>"Deleted application location association ". $Item
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

                    $Location = $this->SearchForLocationByName($Object->{"LOCATION"});
                    $SubLocation = $this->SearchForSubLocationByName($Object->{"SUB-LOCATION"});
                    $result = DB::table("application_location_association")->where("application_id", $this->ApplicationInfo->application_id)->where("sub_location_id",$idToUpdate)->update([
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"UPDATE",
                        "applog_activity_performed_by"=> session()->get("User")->user_username,
                        "applog_activity_desc"=>"Updated application location association ". $this->ApplicationInfo->application_id . ", " . $Location->location_id
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
            "log_activity_desc"=>"Downloaded CSV of Application location association Info"
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
            $PermsDetailed = session()->get("settings-application-location association");
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
        if (!(Cache::has("application-location association"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        $this->LoadOrganizations();
        $this->LoadLocations();
        $this->LoadSubLocations();
        return view('livewire.settings.application-location-association');
    }
}
