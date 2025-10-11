<?php

namespace App\Livewire\Logs;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use \Exception;
use \PDO;
class GeneralLog extends Component
{
    private $conn;

    public $ActivityType = "%";
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $User = "%";
    public $TimeFrame = "LAST 7 DAYS";
    public $TableInfo = [];
    public $headers = [
        "DATE",
        "TIME",
        "ACTIVITY",
        "USER",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";
    public $Users = [];

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
            
            $this->Users = Cache::get("users", collect());
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

            $sql = "SELECT split_part(log_activity_time::text,' ',1) AS date,
                           split_part(log_activity_time::text,' ',2) AS time,
                           log_activity_type,
                           log_activity_performed_by,
                           log_activity_desc
                    FROM log
                    WHERE log_activity_type LIKE :atype
                      AND log_activity_time >= :start
                      AND log_activity_time <= :end
                      AND split_part(log_activity_time::text,' ',2) >= :stime
                      AND split_part(log_activity_time::text,' ',2) <= :etime
                      AND log_activity_performed_by LIKE :user";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":atype" => "%".$this->ActivityType."%",
                ":start" => $this->StartDate,
                ":end" => $this->EndDate,
                ":stime" => $this->StartTime,
                ":etime" => $this->EndTime,
                ":user" => $this->User
            ]);

            $this-> TableInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

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

        $stmt = $this->conn->prepare("INSERT INTO log (log_activity_time, log_activity_type, log_activity_performed_by, log_activity_desc) VALUES (NOW(), 'REPORT', :by, :desc)");
        $stmt->execute([
            ":by" => session()->get("User")->user_username,
            ":desc" => "Downloaded CSV of General Log Info"
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
            $PermsDetailed = session()->get("logs-general log");
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
        return view('livewire.logs.general-log');
    }
}
