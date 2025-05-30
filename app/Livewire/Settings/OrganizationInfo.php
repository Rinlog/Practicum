<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \Exception;
class OrganizationInfo extends Component
{
    public $headers = [
        "SEQ.",
        "ORGANIZATION ID",
        "ORGANIZATION NAME",
        "CIVIC ADDRESS",
        "PHONE",
        "EMAIL",
        "WEBSITE",
        "DESCRIPTION"
    ];
    public $DisplayOrgs = "";
    public function LoadOrgInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $OrgInfo = DB::table("organization")->get();
                $this->DisplayOrgs = "";
                foreach ($OrgInfo as $key => $Org) {
                    $OrgNameAsID = $this->SpaceToUnderScore($Org->organization_name);
                    $this->DisplayOrgs.=
                    "<tr id={$OrgNameAsID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.OrgChecked(\$event,'{$Org->organization_name}')\">
                        </td>
                        <td></td>
                        <td>{$Org->organization_id}</td>
                        <td>{$Org->organization_name}</td>
                        <td>{$Org->organization_civic_address}</td>
                        <td>{$Org->organization_phone}</td>
                        <td>{$Org->organization_email}</td>
                        <td>{$Org->organization_website}</td>
                        <td>{$Org->organization_desc}</td>
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
                if (strtolower($Object->{"ORGANIZATION ID"}) == "will generate automatically"){
                    $Object->{"ORGANIZATION ID"} = Uuid::uuid4()->toString();
                }
                try{
                    $result = DB::table("organization")->insert([
                        "organization_id"=>$Object->{"ORGANIZATION ID"},
                        "organization_name" => $Object->{"ORGANIZATION NAME"},
                        "organization_civic_address"=>$Object->{"CIVIC ADDRESS"},
                        "organization_phone"=> $Object->{"PHONE"},
                        "organization_email"=> $Object->{"EMAIL"},
                        "organization_website"=> $Object->{"WEBSITE"},
                        "organization_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted organization ". $Object->{"ORGANIZATION NAME"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, false);
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("organization")->whereIn("organization_name", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted organization(s): ". $Value
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

                    $result = DB::table("organization")->where("organization_name", $idToUpdate)->update([
                        "organization_name" => $Object->{"ORGANIZATION NAME"},
                        "organization_civic_address"=>$Object->{"CIVIC ADDRESS"},
                        "organization_phone"=> $Object->{"PHONE"},
                        "organization_email"=> $Object->{"EMAIL"},
                        "organization_website"=> $Object->{"WEBSITE"},
                        "organization_desc"=> $Object->{"DESCRIPTION"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated organization ". $Object->{"ORGANIZATION NAME"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
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
            "log_activity_desc"=>"Downloaded CSV of organization Info"
        ]);
    }
    public function render()
    {
        return view('livewire..settings.organization-info');
    }
}
