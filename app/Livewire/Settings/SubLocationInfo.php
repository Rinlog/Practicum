<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \Exception;
class SubLocationInfo extends Component
{
    public $headers = [
        "SEQ.",
        "ORGANIZATION",
        "LOCATION NAME",
        "SUB-LOCATION ID",
        "SUB-LOCATION NAME",
        "CIVIC ADDRESS",
        "FLOOR",
        "LONGITUDE",
        "LATITUDE",
        "ALTITUDE",
        "DESCRIPTION"
    ];
    public $organization = "";
    public $OrgInfo;
    public $Organizations = [];

    public $Location = "";
    public $LocationInfo;
    public $Locations = [];
    public $DisplayTableInfo = "";
    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = DB::table("organization")->where("organization_id", $_SESSION["User"]->organization_id)->firstOrFail();
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
            $organizations = DB::table("organization")->get();
            $this->Organizations = $organizations->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadLocations(){
        try{

            $locations = DB::table("location")->where("organization_id",$this->OrgInfo->organization_id)->get();
            $this->Locations = $locations->toArray();
        }
        catch(Exception $e){
            $this->Locations = "";
        }
    }
    public function setDefaultLocation(){
            //we will set the first location to be default
            $this->Location = $this->Locations[0]->location_name;
            $this->LocationInfo = $this->Locations[0];
    }
    public function SetLocation($locationID){
        $NewLoc = [];
        foreach ($this->Locations as $location){
            if ($locationID == $location->location_id){
                $NewLoc = $location;
            }
        }
        $this->Location = $NewLoc->location_name;
        $this->LocationInfo = $NewLoc;
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
        if (isset($_SESSION["User"])) {
            try{
                $locationInfo = DB::table("sub_location")
                ->select(DB::raw("sub_location_id, location_id, sub_location_name, sub_location_civic_address, sub_location_floor, ST_X(sub_location_geo::geometry) as latitude, ST_Y(sub_location_geo::geometry) as longitude, ST_Z(sub_location_geo::geometry) as altitude, sub_location_desc"))
                ->where("location_id", $this->LocationInfo->location_id)
                ->get();
                $this->DisplayTableInfo = "";
                foreach ($locationInfo as $key => $location) {
                    $TRID = $this->SpaceToUnderScore($location->sub_location_name);
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$location->sub_location_name}')\">
                        </td>
                        <td></td>
                        <td>{$this->organization}</td>
                        <td>{$this->Location}</td>
                        <td>{$location->sub_location_id}</td>
                        <td>{$location->sub_location_name}</td>
                        <td>{$location->sub_location_civic_address}</td>
                        <td>{$location->sub_location_floor}</td>
                        <td>{$location->longitude}</td>
                        <td>{$location->latitude}</td>
                        <td>{$location->altitude}</td>
                        <td>{$location->sub_location_desc}</td>
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
                    if (strtolower($Object->{"SUB-LOCATION ID"}) == "will generate automatically"){
                        $Object->{"SUB-LOCATION ID"} = Uuid::uuid4()->toString();
                    }
                    $geo = null;
                    if (strlen($Object->{"LATITUDE"}) > 0 && strlen($Object->{"LONGITUDE"}) > 0 && strlen($Object->{"ALTITUDE"}) > 0){
                        $lat = $Object->{"LATITUDE"};
                        $lon = $Object->{"LONGITUDE"};
                        $alt = $Object->{"ALTITUDE"};
                        $geo = DB::raw("ST_MakePoint($lat,$lon,$alt)");
                    }
                    $result = DB::table("sub_location")->insert([
                        "sub_location_id"=>$Object->{"SUB-LOCATION ID"},
                        "location_id"=>$this->LocationInfo->location_id,
                        "sub_location_name"=> $Object->{"SUB-LOCATION NAME"},
                        "sub_location_civic_address" => $Object->{"CIVIC ADDRESS"},
                        "sub_location_floor"=>$Object->{"FLOOR"},
                        "sub_location_geo"=> $geo,
                        "sub_location_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted sub location ". $Object->{"SUB-LOCATION ID"}
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
                    $result = DB::table("sub_location")->whereIn("sub_location_name", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted sub location(s): ". $Value
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
                    if (strtolower($Object->{"SUB-LOCATION ID"}) == "will generate automatically"){
                        $Object->{"SUB-LOCATION ID"} = DB::table("sub_location")->where("sub_location_name", $idToUpdate)->value("sub_location_id");
                    }
                    $result = DB::table("sub_location")->where("sub_location_name", $idToUpdate)->update([
                        "sub_location_name"=> $Object->{"SUB-LOCATION NAME"},
                        "sub_location_civic_address" => $Object->{"CIVIC ADDRESS"},
                        "sub_location_floor"=>$Object->{"FLOOR"},
                        "sub_location_geo"=> $geo,
                        "sub_location_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated sub location ". $Object->{"SUB-LOCATION ID"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    Log::channel("customlog")->error($e->getMessage());
                    array_push($Results, 0);
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
            "log_activity_desc"=>"Downloaded CSV of location Info"
        ]);
    }
    public function render()
    {
        return view('livewire..settings.sub-location-info');
    }
}
