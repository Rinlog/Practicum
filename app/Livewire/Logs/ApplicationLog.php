<?php

namespace App\Livewire\Logs;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use \Exception;

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
    public function LoadAllUserInfo(){
        try{
            $this->Users = DB::table("users")->get();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadApplications(){
        try{
            $this->Applications = DB::table("application")->get();
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
            //dd($this->ActivityType . "\n" . $this->StartDate . "\n" .$this->EndDate ."\n" .$this->StartTime . "\n" . $this->EndTime . "\n" . $this->User);
            $TableInfo = DB::table("application_log")
            ->whereLike("applog_activity_type", "%".$this->ActivityType."%")
            ->where("application_id",$this->ApplicationInfo->application_id)
            ->where("applog_activity_time",">=",$this->StartDate)
            ->where("applog_activity_time","<=",$this->EndDate)
            ->where(DB::raw("split_part(applog_activity_time::text,' ',2)"),">=",$this->StartTime)
            ->where(DB::raw("split_part(applog_activity_time::text,' ',2)"),"<=",$this->EndTime)
            ->whereLike("applog_activity_performed_by",$this->User)
            ->get(DB::raw("application_id, split_part(applog_activity_time::text,' ',1) AS date, split_part(applog_activity_time::text,' ',2) as time, applog_activity_type, applog_activity_performed_by, applog_activity_desc" ));
            foreach($TableInfo as $Row){
                $this->DisplayTableInfo .= "
                <tr class= 'cursor-pointer hover:bg-[#f2f2f2]' wire:click='\$js.OpenRowDetails(\"".$this->application."\",\"".$Row->date."\",\"".$Row->time."\",\"".$Row->applog_activity_type."\",\"".$Row->applog_activity_performed_by."\",\"".$Row->applog_activity_desc."\")'>
                <td></td>
                <td>".$this->application."</td>
                <td>".$Row->date."</td>
                <td>".$Row->time."</td>
                <td>".$Row->applog_activity_type."</td>
                <td>".$Row->applog_activity_performed_by."</td>
                <td>".$Row->applog_activity_desc."</td>
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
        DB::table("application_log")->insert([
            "application_id"=>$this->ApplicationInfo->application_id,
            "applog_activity_time"=>now(),
            "applog_activity_type"=>"REPORT",
            "applog_activity_performed_by"=> $_SESSION["User"]->user_username,
            "applog_activity_desc"=>"Downloaded CSV of Application Log Info"
        ]);
    }
    public function render()
    {
        return view('livewire.logs.application-log');
    }
}
