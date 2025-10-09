<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use \Exception;
use Ramsey\Uuid\Uuid;
use \PDO;

#[Title("UserSettings | IDL")]
class UserSettings extends Component
{
    private $conn2;
    private $errStyle = "border-2 border-red-500";
    public $FName = "";
    public $LName = "";
    public $Email = "";
    public $Phone = "";
    public $Username = "";
    public $Organization = "";
    public $Application = "";
    public $Role = "";

    public $CurrentPass = "";
    public $NewPass = "";
    public $ConfirmPass = "";

    public $User;

    public function __construct(){
        $DB2 = config("database.connections.pgsql_2");
        $this->conn2 = new PDO(
            $DB2["driver"].":host=".$DB2["host"]." port=".$DB2["port"]." dbname=".$DB2["database"],
            $DB2["username"],
            $DB2["password"],
            [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    public function LoadGeneralInfo(){
        try{
            $this->FName = explode(" ",$this->User->user_name)[0];
            $this->LName = explode(" ",$this->User->user_name)[1] ?? "";
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

                $users = Cache::get("users", collect());
                $this->User = $users->firstWhere("user_id", $_SESSION["User"]->user_id);
                $_SESSION["User"] = $this->User;
                $this->Username = $this->User->user_username;


                $organizations = Cache::get("organization", collect());
                $org = $organizations->firstWhere("organization_id", $this->User->organization_id);
                $this->Organization = $org ? $org->organization_name : "";


                $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", $this->User->user_id);

                $ApplicationsArray = $userRoles->pluck("application_id")->all();
                if (count($ApplicationsArray) > 0){
                    $applications = Cache::get("application", collect())
                        ->whereIn("application_id", $ApplicationsArray);
                    $this->Application = $applications->pluck("application_name")->implode(", ");
                }

                $roleIds = $userRoles->pluck("role_id")->all();
                if (count($roleIds) > 0){
                    $roles = Cache::get("role", collect())
                        ->whereIn("role_id", $roleIds);
                    $this->Role = $roles->pluck("role_name")->implode(", ");
                }

                $this->LoadGeneralInfo();
            }
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function SaveGeneralInfo(){
        try{

            DB::table("users")
                ->where("user_id", $this->User->user_id)
                ->update([
                    "user_name" => trim($this->FName) . " " . trim($this->LName),
                    "user_email" => trim($this->Email),
                    "user_phone" => trim($this->Phone),
                ]);

            DB::table("log")->insert([
                "log_activity_time" => now(),
                "log_activity_type" => "UPDATE",
                "log_activity_performed_by" => $this->User->user_username,
                "log_activity_desc" => $this->User->user_username . " updated their profile info",
            ]);

            Cache::forget("users");
            Cache::rememberForever("users", fn() => DB::table("users")->get());

            return true;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function GenEncryptedPass($Password){
        $iv = random_bytes(16);
        $secret = Uuid::uuid4()->toString();
        $key = base64_encode(hash("sha256",$secret));
        $key2 = base64_decode($key);

        $encrypted = openssl_encrypt(
            $Password,
            'AES-256-CBC',
            $key2,
            OPENSSL_RAW_DATA,
            $iv
        );
        $encrypted = base64_encode($encrypted);

        return [base64_encode($iv),$key,$encrypted];
    }

    public function DecryptPass($Password, $Salt){

        $stmt = $this->conn2->prepare("SELECT key_data FROM key_vault WHERE key_id = :id");
        $stmt->execute([":id"=>$Salt]);
        $iv_keyRaw = $stmt->fetchColumn();

        $iv_key = explode(",",$iv_keyRaw);
        if (count($iv_key) == 2){
            $iv = base64_decode($iv_key[0]);
            $key = base64_decode($iv_key[1]);
            $DecryptedPass = base64_decode($Password);

            $decrypted = openssl_decrypt(
                $DecryptedPass,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
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
            return $this->CurrentPass == $passdecrepted;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }

    public function ChangePass(){
        try{
            $PasswordInfo = $this->GenEncryptedPass($this->ConfirmPass);


            $users = Cache::get("users", collect());
            $Salt = optional($users->firstWhere("user_id", $this->User->user_id))->user_salt;


            $stmt2 = $this->conn2->prepare("UPDATE key_vault SET key_data = :data WHERE key_id = :id");
            $stmt2->execute([
                ":data" => $PasswordInfo[0] . ',' . $PasswordInfo[1],
                ":id" => $Salt
            ]);


            DB::table("users")
                ->where("user_username", $this->Username)
                ->update([
                    "user_password" => $PasswordInfo[2],
                ]);

            Cache::forget("users");
            Cache::rememberForever("users", fn() => DB::table("users")->get());

            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function render()
    {
        $this->LoadAccountInfo();
        return view('livewire.user-settings');
    }
}
