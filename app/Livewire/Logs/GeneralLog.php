<?php

namespace App\Livewire\Logs;

use DateTime;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;

class GeneralLog extends Component
{
    public $ActivityType = "%";
    public $StartDate = '';
    public $EndDate = '';
    public $StartTime = '00:00';
    public $EndTime = '23:59';
    public $User = "%";
    public $headers = [
        "DATE",
        "TIME",
        "ACTIVITY",
        "USER",
        "DESCRIPTION"
    ];
    public $DisplayTableInfo = "";
    public $Users = [];
    public function LoadAllUserInfo(){
        try{
            $this->Users = DB::table("users")->get();
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
            //dd($this->ActivityType . "\n" . $this->StartDate . "\n" .$this->EndDate ."\n" .$this->StartTime . "\n" . $this->EndTime . "\n" . $this->User);
            $TableInfo = DB::table("log")
            ->whereLike("log_activity_type", "%".$this->ActivityType."%")
            ->where("log_activity_time",">=",$this->StartDate)
            ->where("log_activity_time","<=",$this->EndDate)
            ->where(DB::raw("split_part(log_activity_time::text,' ',2)"),">=",$this->StartTime)
            ->where(DB::raw("split_part(log_activity_time::text,' ',2)"),"<=",$this->EndTime)
            ->whereLike("log_activity_performed_by",$this->User)
            ->get(DB::raw("split_part(log_activity_time::text,' ',1) AS date, split_part(log_activity_time::text,' ',2) as time, log_activity_type, log_activity_performed_by, log_activity_desc" ));
            foreach($TableInfo as $Row){
                $this->DisplayTableInfo .= "
                <tr>
                <td></td>
                <td>".$Row->date."</td>
                <td>".$Row->time."</td>
                <td>".$Row->log_activity_type."</td>
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
    public function LogExport(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION["User"]))) { return null; }
        DB::table("log")->insert([
            "log_activity_time"=>now(),
            "log_activity_type"=>"REPORT",
            "log_activity_performed_by"=> $_SESSION["User"]->user_username,
            "log_activity_desc"=>"Downloaded CSV of General Log Info"
        ]);
    }
    public function render()
    {
        $this->LoadAllUserInfo();
        return view('livewire..logs.general-log');
    }
}
