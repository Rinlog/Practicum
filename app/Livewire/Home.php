<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;
use Ramsey\Uuid\Uuid;
use \DateTime;
use \Exception;
use \PDO;
use PhpOption\None;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

#[Title("Home | IDL")]
class Home extends Component
{
    private $conn;
    public $user;
    public $Applications = [];
    public $application;
    public $ApplicationInfo;
    public $userRoles = [];
    public $DisplayLogTableInfo = "";

    public function __construct()
    {
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

    public function LoadUsersRoles(){
        try{
            $roleAssocs = Cache::get("user_role_association",collect());
            $RoleIds = [];
            foreach ($roleAssocs as $Assoc){
                if ($Assoc->user_id == $this->user->user_id){
                    array_push($RoleIds,$Assoc->role_id);
                }
            }
            $roleTable = Cache::get("role", collect());
            $userRoles = [];
            foreach ($roleTable as $role){
                if (in_array($role->role_id,$RoleIds)){
                    array_push($userRoles,$role);
                }
            }
            $this->userRoles = $userRoles;
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

    public function LoadApplications(){
        try{
            $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", $this->user->user_id);

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
    public function SetApplication($ApplicationID){
        foreach ($this->Applications as $Application){
            if ($Application->application_id == $ApplicationID) {
                $this->ApplicationInfo = $Application;
                $this->application = $Application->application_name;
            }
        }
        if ($ApplicationID != session()->get("AppId")){
            $UserRoleAssoc = Cache::get("user_role_association",collect())->where("user_id",$this->user->user_id);
            $UserRolesBasedOnApp = $UserRoleAssoc->where("application_id",$this->ApplicationInfo->application_id)->pluck("role_id")->values()->toArray(); //using a default value
            $RolePermissionIds = Cache::get("role_permission_association",collect())->whereIn("role_id",$UserRolesBasedOnApp)->pluck("permission_id")->values()->toArray();
            session()->put("AllAppPermsForUser",Cache::get("permission",collect())->whereIn("permission_id",$RolePermissionIds)->values()->toArray());
            session()->put("AppId",$this->ApplicationInfo->application_id);
            return redirect('home');
        }
    }

    public function LoadLogInfo(){
        try{
            $this->DisplayLogTableInfo = '';
            $StartDate = (new DateTime())->modify("-1 days")->format("Y-m-d");
            $EndDate = (new DateTime())->modify("+1 day")->format("Y-m-d");
            $StartDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T00:00:00.000",$StartDate);
            $EndDate = preg_replace('/T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{3}Z/i',"T23:59:00.000",$EndDate);

            $sql = "SELECT split_part(log_activity_time::text,' ',1) AS date,
                           split_part(log_activity_time::text,' ',2) AS time,
                           log_activity_type, log_activity_performed_by, log_activity_desc
                    FROM log
                    WHERE log_activity_time >= :start AND log_activity_time <= :end
                    ORDER BY log_activity_time DESC
                    LIMIT 10";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":start" => $StartDate,
                ":end" => $EndDate
            ]);
            $TableInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach($TableInfo as $Row){
                $this->DisplayLogTableInfo .= "
                <tr>
                <td>".$Row->time."</td>
                <td>".$Row->log_activity_performed_by."</td>
                <td>".$Row->log_activity_desc."</td>
                </tr>
                ";
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function render()
    {
        if (!(Cache::has("users"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        $this->LoadUsersRoles();
        $this->LoadApplications();
        $this->SetApplication(session()->get("AppId"));
        return view('livewire.home');
    }
}
