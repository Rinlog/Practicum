<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Cache;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class ApplicationInfo extends Component
{
    public $headers = [
        "SEQ.",
        "ORGANIZATION NAME",
        "APPLICATION ID",
        "APPLICATION NAME",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $user = "";
    public $applications = "";
    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = collect(Cache::get('organization', collect()))
                    ->firstWhere("organization_id", $_SESSION["User"]->organization_id);
                $this->organization = $organizationInfo->organization_name;
                $this->OrgInfo = $organizationInfo;
            }
            catch(Exception $e){
                $this->organization = "";
            }
        }
    }
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
    public function LoadOrganizations(){
        try{
            $this->Organizations = Cache::get("organization",collect())->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SetOrg($NewOrgID){
        $NewOrg = [];
        foreach ($this->Organizations as $org){
            if ($org->organization_id == $NewOrgID) {
                $NewOrg = $org;
            }
        }
        $this->OrgInfo = $NewOrg;
        $this->organization = $NewOrg->organization_name;
    }
    public function LoadApplicationInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $applicationInfo = Cache::get("application", collect())->where("organization_id",$this->OrgInfo->organization_id);
                $this->applications = "";
                foreach ($applicationInfo as $key => $application) {
                    $ApplicationNameAsID = $this->SpaceToUnderScore($application->application_name);
                    $this->applications.=
                    "<tr id={$ApplicationNameAsID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ApplicationChecked(\$event,'{$application->application_name}')\">
                        </td>
                        <td></td>
                        <td>{$this->organization}</td>
                        <td>{$application->application_id}</td>
                        <td>{$application->application_name}</td>
                        <td>{$application->application_creation_time}</td>
                        <td>{$application->application_created_by}</td>
                        <td>{$application->application_desc}</td>
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
    public function GetApplicationIDsFromNames($names){
        try{
            $result = Cache::get("application", collect())->whereIn("application_name",$names)->values()->toArray();
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
        if (!(isset($_SESSION["User"]))) { return null; }
        $ArrayOfActions = json_decode($actions, true);
        $Results = [];
        foreach ($ArrayOfActions as $action){
            $ActionSplit = explode("~!~", $action);
            $Query = $ActionSplit[0];
            $Value = $ActionSplit[1];
            $organizationID = $this->OrgInfo->organization_id;
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                if (strtolower($Object->{"APPLICATION ID"}) == "will generate automatically"){
                    $Object->{"APPLICATION ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("application")->insert([
                        "application_id"=>$Object->{"APPLICATION ID"},
                        "organization_id"=> $organizationID,
                        "application_name" => $Object->{"APPLICATION NAME"},
                        "application_creation_time"=>now(),
                        "application_created_by"=> $_SESSION["User"]->user_username,
                        "application_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$Object->{"APPLICATION ID"},
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Inserted application ". $Object->{"APPLICATION NAME"}
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
                    DB::table("application_log")->whereIn("application_id",$this->GetApplicationIDsFromNames($ItemsToDelete))->delete();
                    $result = DB::table("application")->whereIn("application_id", $this->GetApplicationIDsFromNames($ItemsToDelete))->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                                "log_activity_desc"=>"Deleted application ". $Item
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

                    //get the id before updating it otherwise the new updated row means the id to update will no longer be valid
                    if (strtolower($Object->{"APPLICATION ID"}) == "will generate automatically"){
                        $Object->{"APPLICATION ID"} = DB::table("application")->where("application_name", $idToUpdate)->value("application_id");
                    }
                    $result = DB::table("application")->where("application_name", $idToUpdate)->update([
                        "application_name" => $Object->{"APPLICATION NAME"},
                        "application_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$Object->{"APPLICATION ID"},
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"UPDATE",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Updated application ". $Object->{"APPLICATION NAME"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
        }
        Cache::forget("application");
        Cache::rememberForever("application", fn() => DB::table("application")->get());
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
            "log_activity_desc"=>"Downloaded CSV of Application Info"
        ]);
    }

    public function render()
    {
        if (!(Cache::has("application"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        return view('livewire.settings.application-info');
    }
}
