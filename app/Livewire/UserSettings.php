<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Exception;
use Ramsey\Uuid\Uuid;
#[Title("UserSettings | IDL")]
class UserSettings extends Component
{
    private $errStyle = "border-2 border-red-500";
    public $FName = "";
    public $LName = "";
    public $Email = "";
    public $Phone = "";
    public $Username = "";
    public $Organization = "";
    public $Application = "";
    public $Role = "";

    //security vars
    public $CurrentPass = "";
    public $NewPass = "";
    public $ConfirmPass = "";

    public $User;
    public function LoadGeneralInfo(){
        try{
            $this->FName = explode(" ",$this->User->user_name)[0];
            $this->LName = explode(" ",$this->User->user_name)[1];
            $this->Email = $this->User->user_email;
            $this->Phone = $this->User->user_phone;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadAccountInfo(){
        try{
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION["User"])){
                $this->User = DB::table("users")->where("user_id",$_SESSION["User"]->user_id)->get()[0];
                $_SESSION["User"] = $this->User;
                //username
                $this->Username=$this->User->user_username;
                //org
                $this->Organization = DB::table("organization")->where("organization_id",$this->User->organization_id)->value("organization_name");
                //applications
                $RoleAssocs = DB::table("user_role_association")->where("user_id",$this->User->user_id)->get();
                $ApplicationsArray = [];
                foreach($RoleAssocs as $RoleAssoc){
                    array_push($ApplicationsArray,$RoleAssoc->application_id);
                }
                $ApplicationNames = DB::table("application")->whereIn("application_id",$ApplicationsArray)->get("application_name");
                $appString = "";
                foreach($ApplicationNames as $appName){
                    $appString .= $appName->application_name . ", ";
                }
                $this->Application = substr($appString,0,strlen($appString)-2);
                //roles
                $roleIds = [];
                foreach ($RoleAssocs as $role){
                    array_push($roleIds,$role->role_id);
                }
                $Roles = DB::table("role")->whereIn("role_id",$roleIds)->get();
                $roleString = "";
                foreach($Roles as $Role){
                    $roleString .= $Role->role_name . ", ";
                }
                $this->Role = substr($roleString,0,strlen($roleString)-2);

            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SaveGeneralInfo(){
        try{
            $result = DB::table("users")->where("user_id",$this->User->user_id)->update([
                "user_name"=>trim($this->FName) . " " . trim($this->LName),
                "user_email"=>trim($this->Email),
                "user_phone"=>trim($this->Phone)
            ]);
            DB::table("log")->insert([
                "log_activity_time"=>now(),
                "log_activity_type"=>"UPDATE",
                "log_activity_performed_by"=> $this->User->user_username,
                "log_activity_desc"=>$this->User->user_username ." updated there profile info"
            ]);
            return $result;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function GenEncryptedPass($Password){
        $iv = random_bytes(16);
        $secret = Uuid::uuid4()->toString();
        $key = hash("sha256",$secret);

        $encrypted = openssl_encrypt(
            $Password,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        $encrypted = base64_encode($encrypted);

        return [base64_encode($iv),base64_encode($key),$encrypted];
    }
    public function DecryptPass($Password, $Salt){
        $iv_keyRaw = DB::connection("pgsql_2")->table("key_vault")->where("key_id",$Salt)->value("key_data");
        $iv_key = explode(",",$iv_keyRaw);
        
        if (count($iv_key) == 2){
            $iv = base64_decode($iv_key[0]);
            $key = base64_decode($iv_key[1]);
            $Password = base64_decode($Password);

            $decrypted = openssl_decrypt(
                $Password,
                'AES-256-CBC',
                $key,
                0,
                $iv
            );
            return $decrypted;
        }
        else{
            return "";
        }
    }
    public function VerifyCurrentPassword(){
        try{
            $passdecrepted = $this->DecryptPass($this->User->user_password,$this->User->user_salt);
            if ($this->CurrentPass == $passdecrepted){
                return true;
            }
            else{
                return false;
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function ChangePass(){
        try{
            $PasswordInfo = $this->GenEncryptedPass($this->ConfirmPass);

            $Salt = DB::table("users")->where("user_id",$this->User->user_id)->value("user_salt");

            $keystore = DB::connection("pgsql_2")->table("key_vault")->where("key_id",$Salt)->update([
                "key_data"=>$PasswordInfo[0] . ',' . $PasswordInfo[1]
            ]);

            DB::table("users")->where("user_username", $this->Username)->update([
                "user_password"=> $PasswordInfo[2]
            ]);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
    public function render()
    {
        $this->LoadAccountInfo();
        $this->LoadGeneralInfo();
        return view('livewire.user-settings');
    }
}
