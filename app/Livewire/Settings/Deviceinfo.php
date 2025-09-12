<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use \Exception;
class Deviceinfo extends Component
{
    public $headers = [
        "SEQ.",
        "DEVICE EUI",
        "DEVICE NAME EUI",
        "TYPE",
        "ORGANIZATION",
        "MODEL",
        "SERIAL NO.",
        "MANUFACTURER",
        "MANUFACTURE DATE",
        "MIN SAMPLING RATE",
        "MAX SAMPLING RATE",
        "MEMORY SIZE",
        "COMMUNICATION PROTOCOL",
        "INTERACTION TYPE",
        "DETECTION TYPE",
        "OUTPUT TYPE",
        "ENCODING METHOD",
        "REQUEST METHOD",
        "DESCRIPTION",
        "IS DEPLOYED"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $devices = "";
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
    public function LoadDeviceInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $deviceInfo = DB::table("device")->where("organization_id", $this->OrgInfo->organization_id)->get();
                $this->devices = "";
                foreach ($deviceInfo as $key => $device) {
                    $deviceDeployed = $device->device_is_deployed ? 'true' : 'false';
                    $this->devices.=
                    "<tr id={$device->device_eui}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.DeviceChecked(\$event,'{$device->device_eui}')\">
                        </td>
                        <td></td>
                        <td>{$device->device_eui}</td>
                        <td>{$device->device_name}</td>
                        <td>{$device->device_type_id}</td>
                        <td>{$this->organization}</td>
                        <td>{$device->device_model}</td>
                        <td>{$device->device_serial_no}</td>
                        <td>{$device->device_manufacturer}</td>
                        <td>{$device->device_manufacture_date}</td>
                        <td>{$device->device_min_sampling_rate}</td>
                        <td>{$device->device_max_sampling_rate}</td>
                        <td>{$device->device_memory_size}</td>
                        <td>{$device->device_communication_protocol}</td>
                        <td>{$device->device_interaction_type}</td>
                        <td>{$device->device_detection_type}</td>
                        <td>{$device->device_output_type}</td>
                        <td>{$device->device_encoding_method}</td>
                        <td>{$device->device_request_method}</td>
                        <td>{$device->device_desc}</td>
                        <td>{$deviceDeployed}</td>
                    </tr>";
                }
            }
            catch(Exception $e){

            }
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
                    $result = DB::table("device")->insert([
                        "device_eui"=>$Object->{"DEVICE EUI"},
                        "organization_id"=> $organizationID,
                        "device_name" => $Object->{"DEVICE NAME EUI"},
                        "device_type_id"=>$Object->{"TYPE"},
                        "device_model"=> $Object->{"MODEL"},
                        "device_serial_no"=> $Object->{"SERIAL NO."},
                        "device_manufacturer"=> $Object->{"MANUFACTURER"},
                        "device_manufacture_date"=> $Object->{"MANUFACTURE DATE"},
                        "device_min_sampling_rate"=> $Object->{"MIN SAMPLING RATE"},
                        "device_max_sampling_rate"=> $Object->{"MAX SAMPLING RATE"},
                        "device_memory_size"=> $Object->{"MEMORY SIZE"},
                        "device_communication_protocol"=> $Object->{"COMMUNICATION PROTOCOL"},
                        "device_interaction_type"=> $Object->{"INTERACTION TYPE"},
                        "device_detection_type" => $Object->{"DETECTION TYPE"},
                        "device_output_type"=> $Object->{"OUTPUT TYPE"},
                        "device_encoding_method"=> $Object->{"ENCODING METHOD"},
                        "device_request_method"=> $Object->{"REQUEST METHOD"},
                        "device_is_deployed"=> $Object->{"IS DEPLOYED"},
                        "device_desc"=> $Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted device ". $Object->{"DEVICE EUI"}
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
                    $result = DB::table("device")->whereIn("device_eui", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted device(s): ". $Value
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

                    $result = DB::table("device")->where("device_eui", $idToUpdate)->update([
                        "device_eui"=>$Object->{"DEVICE EUI"},
                        "organization_id"=> $organizationID,
                        "device_name" => $Object->{"DEVICE NAME EUI"},
                        "device_type_id"=>$Object->{"TYPE"},
                        "device_model"=> $Object->{"MODEL"},
                        "device_serial_no"=> $Object->{"SERIAL NO."},
                        "device_manufacturer"=> $Object->{"MANUFACTURER"},
                        "device_manufacture_date"=> $Object->{"MANUFACTURE DATE"},
                        "device_min_sampling_rate"=> $Object->{"MIN SAMPLING RATE"},
                        "device_max_sampling_rate"=> $Object->{"MAX SAMPLING RATE"},
                        "device_memory_size"=> $Object->{"MEMORY SIZE"},
                        "device_communication_protocol"=> $Object->{"COMMUNICATION PROTOCOL"},
                        "device_interaction_type"=> $Object->{"INTERACTION TYPE"},
                        "device_detection_type" => $Object->{"DETECTION TYPE"},
                        "device_output_type"=> $Object->{"OUTPUT TYPE"},
                        "device_encoding_method"=> $Object->{"ENCODING METHOD"},
                        "device_request_method"=> $Object->{"REQUEST METHOD"},
                        "device_is_deployed"=> $Object->{"IS DEPLOYED"},
                        "device_desc"=> $Object->{"DESCRIPTION"}
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated device ". $Object->{"DEVICE EUI"}
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
            "log_activity_desc"=>"Downloaded CSV of Device Info"
        ]);
    }
    public function render()
    {
        return view('livewire.settings.deviceinfo');
    }
}
