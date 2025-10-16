<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use \Illuminate\Database\RecordNotFoundException;
use \Exception;
use Ramsey\Uuid\Uuid;
use \PDO;
use Illuminate\Support\Facades\Artisan;
#[Title("Login | IDL")]
class Login extends Component
{
    private $conn;
    private $conn2;

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

    public function render()
    {
        if (!(Cache::has("users"))){
            Artisan::call("precache:tables");
        }
        return view('livewire.login');
    }

    public $Username = "";
    public $Password = "";
    public $user;

    private $errStyle = "border-2 border-red-500";
    public $usrCustomStyle = "";
    public $usrShowErr = "hide";
    public $usrErrMsg = "";

    public $passCustomStyle = "";
    public $passShowErr = "hide";
    public $passErrMsg = "";

    private function clear(){
        $this->clearusr();
        $this->clearpass();
    }
    private function clearusr(){
        $this->usrCustomStyle = "";
        $this->usrShowErr = "hide";
        $this->usrErrMsg = "";
    }
    private function clearpass(){
        $this->passCustomStyle = "";
        $this->passShowErr = "hide";
        $this->passErrMsg = "";
    }

    public function CheckForDefaultPass(){
        try{
            if ($this->Username != "" and $this->Password != "") {
                $users = Cache::get("users", collect());
                $this->user = $users->firstWhere("user_username", $this->Username);

                if (!$this->user) {
                    return false;
                }
                $UsersPass = $this->DecryptPass($this->user->user_password, $this->user->user_salt);
                if ("idl123abc" == $UsersPass) {
                    return $this->Password == "idl123abc";
                } 
                else if ($this->user->user_password_change_date <= now()){
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        catch (RecordNotFoundException $e) {
            return false;
        }
        catch(Exception $e){
            dd($e);
            return false;
        }
    }

    public function ChangePass($Password){
        try{
            $PasswordInfo = $this->GenEncryptedPass($Password);

            $stmt2 = $this->conn2->prepare("UPDATE key_vault SET key_data = :data WHERE key_id = :id");
            $stmt2->execute([
                ":data" => $PasswordInfo[0] . ',' . $PasswordInfo[1],
                ":id" => $this->user->user_salt
            ]);

            $stmt1 = $this->conn->prepare("UPDATE users SET user_password = :pass, user_password_change_date = now() + interval '6 months'  WHERE user_username = :uname");
            $stmt1->execute([
                ":pass" => $PasswordInfo[2],
                ":uname" => $this->Username
            ]);

            Cache::forget("users");
            Cache::rememberForever("users", fn() => DB::table("users")->get());
            $this->user = null;
            $this->Password = $Password;
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function login(){
        try{
            if ($this->Username == "") {
                $this->usrCustomStyle = $this->errStyle;
                $this->usrShowErr = "show";
                $this->usrErrMsg = "Username can not be blank";
            } else {
                $this->clearusr();
            }

            if ($this->Password == ""){
                $this->passCustomStyle = $this->errStyle;
                $this->passShowErr = "show";
                $this->passErrMsg = "Password can not be blank";
            } else {
                $this->clearpass();
            }

            if ($this->Username != "" and $this->Password != "") {
                $userNameValid = false;
                $passwordValid = false;

                if (!$this->user) {
                    $users = Cache::get("users", collect());
                    $this->user = $users->firstWhere("user_username", $this->Username);
                }

                if (!$this->user) {
                    $this->usrCustomStyle = $this->errStyle;
                    $this->usrShowErr = "show";
                    $this->usrErrMsg = "User does not exist";
                    return;
                }

                if ($this->user->user_is_disabled == true){
                    $this->usrCustomStyle = $this->errStyle;
                    $this->usrShowErr = "show";
                    $this->usrErrMsg = "User account is disabled";
                    return;
                } else {
                    $this->clearusr();
                }

                if (strcasecmp($this->Username,$this->user->user_username) == 0) {
                    $userNameValid = true;
                    $this->clearusr();
                } else {
                    $this->usrCustomStyle = $this->errStyle;
                    $this->usrShowErr = "show";
                    $this->usrErrMsg = "Incorrect username";
                }

                if ($this->Password == $this->DecryptPass($this->user->user_password,$this->user->user_salt)){
                    $passwordValid = true;
                    $this->clearpass();
                } else {
                    $this->passCustomStyle = $this->errStyle;
                    $this->passShowErr = "show";
                    $this->passErrMsg = "Incorrect password";
                }

                if ($userNameValid and $passwordValid) {
                    $this->clear();
                    session_start();
                    session()->put("UserName",ucfirst(strtolower($this->Username)));
                    session()->put("User",$this->user);
                    session()->put("IsSuperAdmin",$this->user->user_is_super_admin);
                    $UserRoleAssoc = Cache::get("user_role_association",collect())->where("user_id",$this->user->user_id);
                    $ApplicationsArray = $UserRoleAssoc->pluck("application_id")->unique()->all();
                    $UserRolesBasedOnApp = $UserRoleAssoc->where("application_id",$ApplicationsArray[0])->pluck("role_id")->values()->toArray(); //using a default value
                    $RolePermissionIds = Cache::get("role_permission_association",collect())->whereIn("role_id",$UserRolesBasedOnApp)->pluck("permission_id")->values()->toArray();
                    session()->put("AllAppPermsForUser",Cache::get("permission",collect())->whereIn("permission_id",$RolePermissionIds)->values()->toArray());
                    session()->put("AdminComponentID",Cache::get("software_component",collect())->where("component_name","Admin Component")->pluck("component_id")[0]);
                    session()->put("AppId",$ApplicationsArray[0]);
                    session()->put("SaveToDB", str_contains(strtolower($this->Username),"nosave")?false:true);
                    return redirect("/home");
                }
            }
        }
        catch (RecordNotFoundException $e) {
            $this->usrCustomStyle = $this->errStyle;
            $this->usrShowErr = "show";
            $this->usrErrMsg = "User does not exist";
        }
        catch(Exception $e){
            dd($e);
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
            $DecodePass = base64_decode($Password);

            $decrypted = openssl_decrypt(
                $DecodePass,
                'aes-256-cbc',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            return $decrypted;
        } else {
            return "";
        }
    }
}

