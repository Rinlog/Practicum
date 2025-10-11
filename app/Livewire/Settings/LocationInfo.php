<?php

namespace App\Livewire\Settings;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use Illuminate\Support\Facades\Artisan;
class LocationInfo extends Component
{
    public $headers = [
        "SEQ.",
        "ORGANIZATION",
        "LOCATION ID",
        "LOCATION NAME",
        "CIVIC ADDRESS",
        "LONGITUDE",
        "LATITUDE",
        "ALTITUDE",
        "DESCRIPTION"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $DisplayTableInfo = "";
    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $organizationInfo = Cache::get("organization", collect())
                    ->where("organization_id", session()->get("User")->organization_id)
                    ->first();
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
            $organizations = Cache::get("organization", collect());
            $this->Organizations = $organizations->values()->toArray();
        }
        catch(Exception $e){

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
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
                $locationInfo = Cache::get("location", collect())
                    ->where("organization_id", $this->OrgInfo->organization_id)
                    ->all();
                $this->DisplayTableInfo = "";
                foreach ($locationInfo as $key => $location) {
                    $TRID = $this->SpaceToUnderScore($location->location_name);
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$location->location_name}')\">
                        </td>
                        <td></td>
                        <td>{$this->organization}</td>
                        <td>{$location->location_id}</td>
                        <td>{$location->location_name}</td>
                        <td>{$location->location_civic_address}</td>
                        <td>{$location->longitude}</td>
                        <td>{$location->latitude}</td>
                        <td>{$location->altitude}</td>
                        <td>{$location->location_desc}</td>
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
            $organizationID = $this->OrgInfo->organization_id;
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                try{
                    if (strtolower($Object->{"LOCATION ID"}) == "will generate automatically"){
                        $Object->{"LOCATION ID"} = Uuid::uuid4()->toString();
                    }
                    $geo = null;
                    if (strlen($Object->{"LATITUDE"}) > 0 && strlen($Object->{"LONGITUDE"}) > 0 && strlen($Object->{"ALTITUDE"}) > 0){
                        $lat = $Object->{"LATITUDE"};
                        $lon = $Object->{"LONGITUDE"};
                        $alt = $Object->{"ALTITUDE"};
                        $geo = DB::raw("ST_MakePoint($lat,$lon,$alt)");
                    }
                    $result = DB::table("location")->insert([
                        "location_id"=>$Object->{"LOCATION ID"},
                        "organization_id"=> $organizationID,
                        "location_name" => $Object->{"LOCATION NAME"},
                        "location_civic_address"=>$Object->{"CIVIC ADDRESS"},
                        "location_geo"=> $geo,
                        "location_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Inserted location ". $Object->{"LOCATION ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, false);
                    log::channel("customlog")->error($e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("location")->whereIn("location_name", $ItemsToDelete)->delete();

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"DELETE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"Deleted location ". $Item
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

                    $geo = null;
                    if (strlen($Object->{"LATITUDE"}) > 0 && strlen($Object->{"LONGITUDE"}) > 0 && strlen($Object->{"ALTITUDE"}) > 0){
                        $lat = $Object->{"LATITUDE"};
                        $lon = $Object->{"LONGITUDE"};
                        $alt = $Object->{"ALTITUDE"};
                        $geo = DB::raw("ST_MakePoint($lat,$lon,$alt)");
                    }
                    if (strtolower($Object->{"LOCATION ID"}) == "will generate automatically"){
                        $Object->{"LOCATION ID"} = DB::table("location")->where("location_name", $idToUpdate)->value("location_id");
                    }
                    $result = DB::table("location")->where("location_name", $idToUpdate)->update([
                        "location_name" => $Object->{"LOCATION NAME"},
                        "location_civic_address"=>$Object->{"CIVIC ADDRESS"},
                        "location_geo"=> $geo,
                        "location_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated location ". $Object->{"LOCATION ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    Log::channel("customlog")->error($e->getMessage());
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("location");
        Cache::rememberForever("location", fn() => DB::table("location")
        ->select(DB::raw(DB::raw("location_id, organization_id, location_name, location_civic_address, ST_X(location_geo::geometry) as latitude, ST_Y(location_geo::geometry) as longitude, ST_Z(location_geo::geometry) as altitude, location_desc"))
        )->get());
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
            "log_activity_desc"=>"Downloaded CSV of location Info"
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
            $PermsDetailed = session()->get("settings-location info");
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
        if (!(Cache::has("location"))){
            Artisan::call("precache:tables");
        }
        return view('livewire..settings.location-info');
    }
}
