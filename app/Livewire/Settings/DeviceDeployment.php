<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Artisan;
use \Exception;

class DeviceDeployment extends Component
{
    public $headers = [
        "SEQ.",
        "DEPLOYMENT ID",
        "DEVICE NAME",
        "LOCATION NAME",
        "SUB LOCATION NAME",
        "LONGITUDE",
        "LATITUDE",
        "ALTITUDE",
        "IP ADDRESS",
        "DATA PORT",
        "DEPLOYMENT TIME",
        "DEPLOYED BY",
        "DESCRIPTION",
        "LATEST DEPLOYMENT",
        "DEVICE DEPLOYMENT DATA"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $DisplayTableInfo = "";

    public $Locations = [];
    public $Devices = [];
    public $SubLocations;
    public $user;
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
        $this->LoadDevices();
        $this->LoadLocations();
    }
    public function LoadDevices(){
        try{
            $this->Devices = Cache::get("device",collect())->where("organization_id",$this->OrgInfo->organization_id)->values()->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadLocations(){
        try{
            $this->Locations = Cache::get("location",collect())->where("organization_id",$this->OrgInfo->organization_id)->values()->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadSubLocations(){
        try{
            $this->SubLocations = Cache::get("sub_location", collect())->values()->toArray();
        }
        catch(Exception $e){

        }
    }
    public function SearchForDevice($deviceID){
        try{
            foreach( $this->Devices as $device ){
                if ($device->device_eui == $deviceID) {
                    return $device;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SearchForDeviceByName($deviceName){
        try{
            foreach( $this->Devices as $device ){
                if ($device->device_name == $deviceName) {
                    return $device;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
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
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $DeploymentInfo = Cache::get("device_deployment",collect()
                ->where("device.organization_id", $this->OrgInfo->organization_id));

                $this->DisplayTableInfo = "";
                foreach ($DeploymentInfo as $key => $Deployment) {
                    $TRID = $this->SpaceToUnderScore($Deployment->device_eui);
                    $Device = $this->SearchForDevice($Deployment->device_eui);
                    $Location = $this->SearchForLocation($Deployment->location_id);
                    $SubLocation = $this->SearchForSubLocation($Deployment->sub_location_id);
                    $IsLatest = $Deployment->deploy_is_latest ? 'true' :'false';
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$Deployment->device_eui}')\">
                        </td>
                        <td></td>
                        <td>{$Deployment->deploy_id}</td>
                        <td>{$Device->device_name}</td>
                        <td>{$Location->location_name}</td>
                        <td>{$SubLocation->sub_location_name}</td>
                        <td>{$Deployment->longitude}</td>
                        <td>{$Deployment->latitude}</td>
                        <td>{$Deployment->altitude}</td>
                        <td>{$Deployment->deploy_ip_address}</td>
                        <td>{$Deployment->deploy_data_port}</td>
                        <td>{$Deployment->deploy_time}</td>
                        <td>{$Deployment->deploy_deployed_by}</td>
                        <td>{$Deployment->deploy_desc}</td>
                        <td>{$IsLatest}</td>
                        <td>{$Deployment->deploy_device_data}</td>
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
            $organizationID = $this->OrgInfo->organization_id;
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                try{
                    $Device = $this->SearchForDeviceByName($Object->{"DEVICE NAME"});
                    $Location = $this->SearchForLocationByName($Object->{"LOCATION NAME"});
                    $SubLocation = $this->SearchForSubLocationByName($Object->{"SUB LOCATION NAME"});
                    if (strtolower($Object->{"DEPLOYMENT ID"}) == "will generate automatically"){
                        $Object->{"DEPLOYMENT ID"} = Uuid::uuid4()->toString();
                    }
                    $geo = null;
                    if (strlen($Object->{"LATITUDE"}) > 0 && strlen($Object->{"LONGITUDE"}) > 0 && strlen($Object->{"ALTITUDE"}) > 0){
                        $lat = $Object->{"LATITUDE"};
                        $lon = $Object->{"LONGITUDE"};
                        $alt = $Object->{"ALTITUDE"};
                        $geo = DB::raw("ST_MakePoint($lat,$lon,$alt)");
                    }
                    $result = DB::table("device_deployment")->insert([
                        "deploy_id"=>$Object->{"DEPLOYMENT ID"},
                        "device_eui"=> $Device->device_eui,
                        "deploy_time" => now(),
                        "location_id"=>$Location->location_id,
                        "sub_location_id"=>$SubLocation->sub_location_id,
                        "deploy_ip_address"=>$Object->{"IP ADDRESS"},
                        "deploy_geo"=>$geo,
                        "deploy_deployed_by"=>$_SESSION["User"]->user_username,
                        "deploy_is_latest"=>$Object->{"LATEST DEPLOYMENT"},
                        "deploy_device_data"=>$Object->{"DEVICE DEPLOYMENT DATA"},
                        "deploy_data_port"=>$Object->{"DATA PORT"},
                        "deploy_desc"=>$Object->{"DESCRIPTION"},
                    ]);
                    $result2 = DB::table("device")->where("device_eui","$Device->device_eui")->update([
                        "device_is_deployed"=>$Object->{"LATEST DEPLOYMENT"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deployed device ". $Object->{"DEPLOYMENT ID"}
                    ]);
                    if ($result == true && $result2 == 1) {
                        array_push($Results, $result);
                    }
                    else{
                        array_push($Results, false);
                    }
                }
                catch(Exception $e){
                    array_push($Results, false);
                    log::channel("customlog")->error($e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    //using device_eui since you should only be able to deploy a device once...
                    $result = DB::table("device_deployment")->whereIn("device_eui", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted device deployment(s): ". $Value
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
                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));

                    $geo = null;
                    if (strlen($Object->{"LATITUDE"}) > 0 && strlen($Object->{"LONGITUDE"}) > 0 && strlen($Object->{"ALTITUDE"}) > 0){
                        $lat = $Object->{"LATITUDE"};
                        $lon = $Object->{"LONGITUDE"};
                        $alt = $Object->{"ALTITUDE"};
                        $geo = DB::raw("ST_MakePoint($lat,$lon,$alt)");
                    }
                    if (strtolower($Object->{"DEPLOYMENT ID"}) == "will generate automatically"){
                        $Object->{"DEPLOYMENT ID"} = DB::table("device_deployment")->where("device_eui", $idToUpdate)->value("deploy_id");
                    }

                    $Location = $this->SearchForLocationByName($Object->{"LOCATION NAME"});
                    $SubLocation = $this->SearchForSubLocationByName($Object->{"SUB LOCATION NAME"});
                    $result = DB::table("device_deployment")->where("device_eui", $idToUpdate)->update([
                        "location_id"=>$Location->location_id,
                        "sub_location_id"=>$SubLocation->sub_location_id,
                        "deploy_ip_address"=>$Object->{"IP ADDRESS"},
                        "deploy_geo"=>$geo,
                        "deploy_is_latest"=>$Object->{"LATEST DEPLOYMENT"},
                        "deploy_data_port"=>$Object->{"DATA PORT"},
                        "deploy_desc"=>$Object->{"DESCRIPTION"},
                    ]);
                    $result2 = DB::table("device")->where("device_eui",$idToUpdate)->update([
                        "device_is_deployed"=>$Object->{"LATEST DEPLOYMENT"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated device deployment ". $Object->{"DEPLOYMENT ID"}
                    ]);
                    if ($result == 1 && $result2 == 1){
                        array_push($Results, $result);
                    }
                    else{
                        array_push($Results, 0);
                    }
                }
                catch(Exception $e){
                    Log::channel("customlog")->error($e->getMessage());
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("device_deployment");
        Cache::rememberForever("device_deployment", fn() => DB::table("device_deployment")
                    ->select(DB::raw("deploy_id, device.device_eui, deploy_time, location_id, sub_location_id, deploy_ip_address,  ST_X(deploy_geo::geometry) as latitude, ST_Y(deploy_geo::geometry) as longitude, ST_Z(deploy_geo::geometry) as altitude, deploy_deployed_by, deploy_is_latest, deploy_device_data, deploy_data_port, deploy_desc"))
                    ->join("device","device.device_eui","=","device_deployment.device_eui")
                    ->get());
        Cache::forget("device");
        Cache::rememberForever("device", fn() => DB::table("device")->get());
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
            "log_activity_desc"=>"Downloaded CSV of Device Deployment Info"
        ]);
    }
    public function render()
    {
        if (!(Cache::has("device_deployment"))){
            Artisan::call("precache:tables");
        }
        $this->LoadSubLocations();
        $this->LoadUserInfo();
        return view('livewire..settings.device-deployment');
    }
}
