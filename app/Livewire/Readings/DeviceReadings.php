<?php

namespace App\Livewire\Readings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use \Exception;
use \PDO;
use \DateTime;
class DeviceReadings extends Component
{
    private $conn;

    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $TimeFrame = "TODAY";
    public $device;
    public $deviceInfo;
    public $devices = [];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $TableInfo = [];
    public $headers = [
        "DEVICE NAME",
        "DATE",
        "TIME",
        "DEPLOYMENT DATA",
        "READING"
    ];

    public function __construct(){
        $DB1 = config("database.connections.pgsql");
        $this->conn = new PDO(
            $DB1["driver"].":host=".$DB1["host"]." port=".$DB1["port"]." dbname=".$DB1["database"],
            $DB1["username"],
            $DB1["password"],
            [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
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
            $this->Organizations = Cache::get("organization",collect())->values()->toArray();
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
        $this->LoadDevicesBasedOnOrg();
    }

    public function LoadDevicesBasedOnOrg(){
        try{

            $this->devices = Cache::get("device", collect())->where("organization_id",$this->OrgInfo->organization_id)->values()->toArray();

            if(count($this->devices) > 0){
                $this->device = $this->devices[0]->device_name;
                $this->deviceInfo = $this->devices[0];
            }
            else{
                $this->device = "NONE";
                $this->deviceInfo = null;
            }
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
            $this->StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$this->StartDate);
            $this->EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$this->EndDate);
           $sql = "SELECT split_part(device_reading_time::text,' ',1) AS date,
                           split_part(device_reading_time::text,' ',2) AS time,
                           device_reading.device_eui,
                           device_reading_data,
                           device_deployment.deploy_device_data
                    FROM device_reading
                    inner JOIN device_deployment on device_reading.deploy_id = device_deployment.deploy_id
                    WHERE 
                      device_reading_time >= :start
                      AND device_reading_time <= :end
                      AND split_part(device_reading_time::text,' ',2) >= :stime
                      AND split_part(device_reading_time::text,' ',2) <= :etime
                      AND device_reading.device_eui = :device
                      ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime,
                ":device" => $this->deviceInfo->device_eui
            ]);

            $this->TableInfo = $stmt->fetchAll(PDO::FETCH_OBJ);
            
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

        $stmt = $this->conn->prepare("INSERT INTO log (log_activity_time, log_activity_type, log_activity_performed_by, log_activity_desc) VALUES (NOW(), 'REPORT', :by, :desc)");
        $stmt->execute([
            ":by" => $_SESSION["User"]->user_username,
            ":desc" => "Downloaded CSV of Device Reading Info"
        ]);
    }
    public function LoadSmallStuff(){
        try{    
            $this->LoadUsersOrganization();
            $this->LoadDevicesBasedOnOrg();
            $this->LoadOrganizations();
        }
        catch(Exception $e){
            
        }
    }
    public function render()
    {
        return view('livewire.readings.device-readings');
    }
}
