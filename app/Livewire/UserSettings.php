<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use \Exception;
use Ramsey\Uuid\Uuid;
use \PDO;

#[Title("UserSettings | IDL")]
class UserSettings extends Component
{
    private $conn;
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
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = :id");
                $stmt->execute([":id" => $_SESSION["User"]->user_id]);
                $this->User = $stmt->fetch(PDO::FETCH_OBJ);
                $_SESSION["User"] = $this->User;

                $this->Username=$this->User->user_username;

                $stmt = $this->conn->prepare("SELECT organization_name FROM organization WHERE organization_id = :oid");
                $stmt->execute([":oid" => $this->User->organization_id]);
                $this->Organization = $stmt->fetchColumn();

                $stmt = $this->conn->prepare("SELECT * FROM user_role_association WHERE user_id = :uid");
                $stmt->execute([":uid" => $this->User->user_id]);
                $RoleAssocs = $stmt->fetchAll(PDO::FETCH_OBJ);

                $ApplicationsArray = [];
                foreach($RoleAssocs as $RoleAssoc){
                    $ApplicationsArray[] = $RoleAssoc->application_id;
                }
                if(count($ApplicationsArray) > 0){
                    $placeholders = str_repeat('?,', count($ApplicationsArray) - 1) . '?';
                    $stmt = $this->conn->prepare("SELECT application_name FROM application WHERE application_id IN ($placeholders)");
                    $stmt->execute($ApplicationsArray);
                    $ApplicationNames = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $appString = "";
                    foreach($ApplicationNames as $appName){
                        $appString .= $appName->application_name . ", ";
                    }
                    $this->Application = substr($appString,0,strlen($appString)-2);
                }

                $roleIds = [];
                foreach ($RoleAssocs as $role){
                    $roleIds[] = $role->role_id;
                }
                if(count($roleIds) > 0){
                    $placeholders = str_repeat('?,', count($roleIds) - 1) . '?';
                    $stmt = $this->conn->prepare("SELECT * FROM role WHERE role_id IN ($placeholders)");
                    $stmt->execute($roleIds);
                    $Roles = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $roleString = "";
                    foreach($Roles as $Role){
                        $roleString .= $Role->role_name . ", ";
                    }
                    $this->Role = substr($roleString,0,strlen($roleString)-2);
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
            $stmt = $this->conn->prepare("UPDATE users SET user_name = :name, user_email = :email, user_phone = :phone WHERE user_id = :id");
            $stmt->execute([
                ":name" => trim($this->FName) . " " . trim($this->LName),
                ":email" => trim($this->Email),
                ":phone" => trim($this->Phone),
                ":id" => $this->User->user_id
            ]);

            $stmt = $this->conn->prepare("INSERT INTO log (log_activity_time, log_activity_type, log_activity_performed_by, log_activity_desc) VALUES (NOW(), 'UPDATE', :by, :desc)");
            $stmt->execute([
                ":by" => $this->User->user_username,
                ":desc" => $this->User->user_username . " updated their profile info"
            ]);

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

            $stmt = $this->conn->prepare("SELECT user_salt FROM users WHERE user_id = :id");
            $stmt->execute([":id" => $this->User->user_id]);
            $Salt = $stmt->fetchColumn();

            $stmt2 = $this->conn2->prepare("UPDATE key_vault SET key_data = :data WHERE key_id = :id");
            $stmt2->execute([
                ":data" => $PasswordInfo[0] . ',' . $PasswordInfo[1],
                ":id" => $Salt
            ]);

            $stmt = $this->conn->prepare("UPDATE users SET user_password = :pass WHERE user_username = :uname");
            $stmt->execute([
                ":pass" => $PasswordInfo[2],
                ":uname" => $this->Username
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
        return view('livewire.user-settings');
    }
}
