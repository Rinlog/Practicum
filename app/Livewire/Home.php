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
            $stmt = $this->conn->prepare("SELECT role_id FROM user_role_association WHERE user_id = :uid");
            $stmt->execute([":uid" => $this->user->user_id]);
            $roleAssoc = $stmt->fetchAll(PDO::FETCH_OBJ);

            $roleIds = array_map(function($r){ return $r->role_id; }, $roleAssoc);

            if(count($roleIds) > 0){
                $placeholders = str_repeat('?,', count($roleIds) - 1) . '?';
                $stmt = $this->conn->prepare("SELECT role_name FROM role WHERE role_id IN ($placeholders)");
                $stmt->execute($roleIds);
                $this->userRoles = $stmt->fetchAll(PDO::FETCH_OBJ);
            } else {
                $this->userRoles = [];
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

    public function LoadApplications(){
        try{
            $stmt = $this->conn->query("SELECT application_id, application_name FROM application");
            $this->Applications = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function setDefaultApplication(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $this->application = $this->Applications[0]->application_name;
                $this->ApplicationInfo = $this->Applications[0];
            }
            catch(Exception $e){
                $this->application = "";
            }
        }
    }

    public function LoadLogInfo(){
        try{
            $this->DisplayLogTableInfo = '';
            $StartDate = new DateTime()->modify("-1 days")->format("Y-m-d");
            $EndDate = new DateTime()->modify("+1 day")->format("Y-m-d");
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
        $this->LoadUserInfo();
        return view('livewire.home');
    }
}
