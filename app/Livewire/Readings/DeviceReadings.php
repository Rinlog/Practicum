<?php

namespace App\Livewire\Readings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;
class DeviceReadings extends Component
{
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $TimeFrame = "LAST 7 DAYS";
    public $device;
    public $deviceInfo;
    public $devices = [];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $headers = [
        "DEVICE NAME",
        "DATE",
        "TIME",
        "DEPLOYMENT DATA",
        "READING"
    ];
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
            $this->dispatch('$refresh');
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
    public function LoadDevicesBasedOnOrg(){
        try{
            $this->devices = DB::table("device")->where("organization_id",$this->OrgInfo->organization_id)->get();
            $this->device = $this->devices[0]->device_name;
            $this->deviceInfo = $this->devices[0];
            
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SetDevice($NewDeviceEUI){
        try{
            foreach ($this->devices as $device){
                if ($device->device_eui == $NewDeviceEUI) {
                    $this->device = $device->device_name;
                    $this->deviceInfo = $device;
                }
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadInfo(){
        try{
            $this->DisplayTableInfo = '';
            $this->StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$this->StartDate);
            $this->EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$this->EndDate);

            $TableInfo = DB::table("device_reading")
            ->where("device_reading_time",">=",$this->StartDate)
            ->where("device_reading_time","<=",$this->EndDate)
            ->where(DB::raw("split_part(device_reading_time::text,' ',2)"),">=",$this->StartTime)
            ->where(DB::raw("split_part(device_reading_time::text,' ',2)"),"<=",$this->EndTime)
            ->where("device_reading.device_eui", $this->deviceInfo->device_eui)
            ->join('device','device_reading.device_eui','=','device.device_eui')
            ->join('device_deployment','device_reading.deploy_id','=','device_deployment.deploy_id')
            ->get(DB::raw("split_part(device_reading_time::text,' ',1) AS date, split_part(device_reading_time::text,' ',2) as time, device.device_name, device_deployment.deploy_device_data, device_reading_data" ));

            foreach($TableInfo as $Row){
                $this->DisplayTableInfo .= "
                <tr>
                <td></td>
                <td>".$Row->device_name."</td>
                <td>".$Row->date."</td>
                <td>".$Row->time."</td>
                <td>".$Row->deploy_device_data."</td>
                <td>".$Row->device_reading_data."</td>
                </tr>
                ";
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
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
            "log_activity_desc"=>"Downloaded CSV of Device Reading Info"
        ]);
    }
    public function render()
    {
        return view('livewire.readings.device-readings');
    }
}
