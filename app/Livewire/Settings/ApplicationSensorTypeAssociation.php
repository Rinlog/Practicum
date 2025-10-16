<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Artisan;
use \Exception;

class ApplicationSensorTypeAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "APPLICATION",
        "SENSOR TYPE",
        "CREATION TIME",
        "CREATED BY",
        "DESCRIPTION"
    ];
    public $application = "";
    public $Applications = [];
    public $ApplicationInfo;
    public $user = "";
    public $DisplayTableInfo = "";
    public $SensorTypeInfo;

    public function LoadUserInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try {
                $this->user = session()->get("User");
            } catch(Exception $e) {
                $this->user = "";
            }
        }
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
            try {
                if (!empty($this->Applications)) {
                    $this->application = $this->Applications[0]->application_name;
                    $this->ApplicationInfo = $this->Applications[0];
                }
            } catch(Exception $e) {
                $this->application = "";
            }
        }
    }

    public function SetApplication($ApplicationID){
        foreach ($this->Applications as $Application){
            if ($Application->application_id == $ApplicationID) {
                $this->ApplicationInfo = $Application;
                $this->application = $Application->application_name;
            }
        }
    }

    public function LoadSensorTypeInfo(){
        try {
            $this->SensorTypeInfo = Cache::get("sensor_type", collect());
        } catch(Exception $e) {
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SearchForSensorType(string $typeID){
        try {
            foreach($this->SensorTypeInfo as $value){
                if ($value->sensor_type_id == $typeID){
                    return $value;
                }
            }
            return null;
        } catch(Exception $e) {
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SearchForSensorTypeByName(string $Name){
        try {
            foreach($this->SensorTypeInfo as $value){
                if ($value->sensor_type == $Name){
                    return $value;
                }
            }
            return null;
        } catch(Exception $e) {
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try {
                $assocInfo = Cache::get("application_sensor_type_association", collect())
                    ->where("application_id", $this->ApplicationInfo->application_id);

                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $assoc) {
                    $SensorType = $this->SearchForSensorType($assoc->sensor_type_id);
                    $this->DisplayTableInfo .=
                    "<tr id={$assoc->sensor_type_id}>
                        <td>
                            <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->sensor_type_id}')\">
                        </td>
                        <td></td>
                        <td>{$this->application}</td>
                        <td>{$SensorType->sensor_type}</td>
                        <td>{$assoc->assoc_creation_time}</td>
                        <td>{$assoc->assoc_created_by}</td>
                        <td>{$assoc->assoc_desc}</td>
                    </tr>";
                }
            } catch(Exception $e) {
                Log::channel("customlog")->error($e->getMessage());
            }
        }
    }

    public function SpaceToUnderScore($input){
        try {
            return str_replace(" ","_", $input);
        } catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function GetApplicationIDsFromNames($names){
        try {
            $applications = Cache::get("application", collect());
            $result = $applications->whereIn("application_name", $names);
            $ArrayOfIDs = [];
            foreach ($result as $value) {
                $ArrayOfIDs[] = $value->application_id;
            }
            return $ArrayOfIDs;
        } catch(Exception $e) {
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
                try {
                    $SensorType = $this->SearchForSensorTypeByName($Object->{"SENSOR TYPE"});
                    $result = DB::table("application_sensor_type_association")->insert([
                        "application_id"=> $this->ApplicationInfo->application_id,
                        "sensor_type_id"=> $SensorType->sensor_type_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>session()->get("User")->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> session()->get("User")->user_username,
                        "applog_activity_desc"=>"associated sensor type ".$SensorType->sensor_type_id." with application " . $this->application
                    ]);
                    $Results[] = $result;
                } catch(Exception $e){
                    $Results[] = false;
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try {
                    $result = DB::table("application_sensor_type_association")
                        ->where("application_id", $this->ApplicationInfo->application_id)
                        ->whereIn("sensor_type_id", $ItemsToDelete)
                        ->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                           DB::table("application_log")->insert([
                                "application_id" => $this->ApplicationInfo->application_id,
                                "applog_activity_time"=>now(),
                                "applog_activity_type"=>"DELETE",
                                "applog_activity_performed_by"=> session()->get("User")->user_username,
                                "applog_activity_desc"=>"Deleted application sensor type association ". $Item
                            ]);
                        }
                    });
                    $Results[] = $result;
                } catch(Exception $e){
                    $Results[] = 0;
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"update")){
                try {
                    $Object = json_decode($Value);
                    $bracketloc = strpos($Query,"[");
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));

                    $result = DB::table("application_sensor_type_association")
                        ->where("application_id", $this->ApplicationInfo->application_id)
                        ->where("sensor_type_id",$idToUpdate)
                        ->update([
                            "assoc_desc"=> $Object->{"DESCRIPTION"},
                        ]);

                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"UPDATE",
                        "applog_activity_performed_by"=> session()->get("User")->user_username,
                        "applog_activity_desc"=>"Updated application sensor type association ". $this->ApplicationInfo->application_id . ", " . $Object->{"SENSOR TYPE"}
                    ]);
                    $Results[] = $result;
                } catch(Exception $e){
                    $Results[] = 0;
                    Log::channel("customlog")->error("". $e->getMessage());
                }
            }
        }
        Cache::forget("application_sensor_type_association");
        Cache::rememberForever("application_sensor_type_association", fn() => DB::table("application_sensor_type_association")->get());
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
            "log_activity_desc"=>"Downloaded CSV of Application sensor type association Info"
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
            $PermsDetailed = session()->get("settings-application-sensor type association");
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
        if (!(Cache::has("application_sensor_type_association"))){
            Artisan::call("precache:tables");
        }
        $this->LoadSensorTypeInfo();
        $this->LoadUserInfo();
        return view('livewire.settings.application-sensor-type-association');
    }
}
