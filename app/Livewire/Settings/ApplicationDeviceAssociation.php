<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;
use SebastianBergmann\Type\TrueType;

class ApplicationDeviceAssociation extends Component
{
    public $headers = [
        "SEQ.",
        "APPLICATION",
        "DEVICE NAME",
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
    public $Devices;
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
            $organizations = DB::table("organization")->get();
            $this->Organizations = $organizations->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadDevices(){
        try{
            $this->Devices = DB::table("device")->get();
        }
        catch(Exception $e){

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
    public function SearchForDevice($DeviceID){
        try{
            foreach ($this->Devices as $Device){
                if ($Device->device_eui == $DeviceID) {
                    return $Device;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForDeviceByName($DeviceName){
        try{
            foreach ($this->Devices as $Device){
                if ($Device->device_name == $DeviceName) {
                    return $Device;
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
        if (isset($_SESSION["User"])) {
            try{
                $assocInfo = DB::table("application_device_association")->where("application_id", $this->ApplicationInfo->application_id)->get();
                $this->DisplayTableInfo = "";
                foreach ($assocInfo as $key => $assoc) {
                    $Device = $this->SearchForDevice($assoc->device_eui);
                    $this->DisplayTableInfo.=
                    "<tr id={$assoc->device_eui}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$assoc->device_eui}')\">
                        </td>
                        <td></td>
                        <td>{$this->application}</td>
                        <td>{$Device->device_name}</td>
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
            $result = DB::table("application")->wherein("application_name",$names)->get();
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
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                try{
                    $Device = $this->SearchForDeviceByName($Object->{"DEVICE NAME"});
                    $result = DB::table("application_device_association")->insert([
                        "device_eui"=> $Device->device_eui,
                        "application_id"=> $this->ApplicationInfo->application_id,
                        "assoc_creation_time" => now(),
                        "assoc_created_by"=>$_SESSION["User"]->user_username,
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"INSERT",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"associated device ".$Device->device_eui." with application " . $this->application
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
                    $result = DB::table("application_device_association")->where("application_id", $this->ApplicationInfo->application_id)->whereIn("device_eui", $ItemsToDelete)->delete();

                    DB::table("application_log")->insert([
                        "application_id" => $this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"DELETE",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Deleted application device association(s) ". $Value
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

                    $Device = $this->SearchForDeviceByName($Object->{"DEVICE NAME"});
                    $result = DB::table("application_device_association")->where("application_id", $this->ApplicationInfo->application_id)->where("device_eui",$idToUpdate)->update([
                        "assoc_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("application_log")->insert([
                        "application_id"=>$this->ApplicationInfo->application_id,
                        "applog_activity_time"=>now(),
                        "applog_activity_type"=>"UPDATE",
                        "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "applog_activity_desc"=>"Updated application device association ". $this->ApplicationInfo->application_id . ", " . $Device->device_eui
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
            "log_activity_desc"=>"Downloaded CSV of Application sensor type association Info"
        ]);
    }
    public function render()
    {
        $this->LoadUserInfo();
        $this->LoadOrganizations();
        $this->LoadDevices();
        return view('livewire..settings.application-device-association');
    }
}
