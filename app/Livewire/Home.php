<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \DateTime;
use \Exception;
#[Title("Home | IDL")]
class Home extends Component
{
    public $user;

    public $Applications = [];
    public $application;
    public $ApplicationInfo;
    public $userRoles = [];
    public $DisplayLogTableInfo = "";
    public function LoadUsersRoles(){
        try{
            $roleAssoc = DB::table("user_role_association")->where("user_id",$this->user->user_id)->get("role_id");
            $roleIds = [];
            foreach ($roleAssoc as $role){
                array_push($roleIds,$role->role_id);
            }
            $this->userRoles = DB::table("role")->whereIn("role_id",$roleIds)->get("role_name");

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
            $applications = DB::table("application")
            ->select("application_id","application_name")
            ->get();
            $this->Applications = $applications->toArray();
        }
        catch(Exception $e){

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
            $TableInfo = DB::table("log")
            ->where("log_activity_time",">=",$StartDate)
            ->where("log_activity_time","<=",$EndDate)
            ->orderBy("log_activity_time","desc")
            ->limit(10)
            ->get(DB::raw("split_part(log_activity_time::text,' ',1) AS date, split_part(log_activity_time::text,' ',2) as time, log_activity_type, log_activity_performed_by, log_activity_desc" ));
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
        $this->LoadUsersRoles();
        return view('livewire.home');
    }
}
