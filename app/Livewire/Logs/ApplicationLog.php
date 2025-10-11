<?php

namespace App\Livewire\Logs;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use \Exception;
use \PDO;
use Illuminate\Support\Facades\Cache;
class ApplicationLog extends Component
{
    public $ActivityType = "%";
    public $StartDate = '';
    public $EndDate = '';
    public $TimeFrame = "LAST 7 DAYS";
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $User = "%";
    public $headers = [
        "APPLICATION",
        "DATE",
        "TIME",
        "ACTIVITY",
        "USER",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";
    public $Users = [];
    public $Applications = [];
    public $application = "";
    public $ApplicationInfo;
    public $TableInfo = [];
    private $conn;

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

    public function LoadAllUserInfo(){
        try{
            
            $this->Users = Cache::get("users", collect())->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function LoadApplications(){
        try{
            $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", session("User")->user_id);

            $ApplicationsArray = $userRoles->pluck("application_id")->all();
            if (count($ApplicationsArray) > 0){
                $this->Applications = Cache::get("application", collect())
                    ->whereIn("application_id", $ApplicationsArray);
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SetDefaultApplication(){
        try{
            $this->ApplicationInfo = $this->Applications[0];
            $this->application = $this->Applications[0]->application_name;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
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

    public function LoadInfo(){
        try{
            $this->DisplayTableInfo = '';
            $this->StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$this->StartDate);
            $this->EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$this->EndDate);

            $sql = "
                SELECT application_id,
                       split_part(applog_activity_time::text,' ',1) AS date,
                       split_part(applog_activity_time::text,' ',2) as time,
                       applog_activity_type,
                       applog_activity_performed_by,
                       applog_activity_desc
                FROM application_log
                WHERE applog_activity_type ILIKE :atype
                  AND application_id = :appid
                  AND applog_activity_time >= :sdate
                  AND applog_activity_time <= :edate
                  AND split_part(applog_activity_time::text,' ',2) >= :stime
                  AND split_part(applog_activity_time::text,' ',2) <= :etime
                  AND applog_activity_performed_by ILIKE :user
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":atype" => "%".$this->ActivityType."%",
                ":appid" => $this->ApplicationInfo->application_id,
                ":sdate" => $this->StartDate,
                ":edate" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime,
                ":user"  => $this->User
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
        if (!(session()->get("User"))) { return null; }

        try {
            $stmt = $this->conn->prepare("
                INSERT INTO application_log (application_id, applog_activity_time, applog_activity_type, applog_activity_performed_by, applog_activity_desc)
                VALUES (:appid, NOW(), 'REPORT', :user, 'Downloaded CSV of Application Log Info')
            ");
            $stmt->execute([
                ":appid" => $this->ApplicationInfo->application_id,
                ":user"  => session()->get("User")->user_username
            ]);
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
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
            $PermsDetailed = session()->get("logs-application-specific log");
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
        return view('livewire.logs.application-log');
    }
}
