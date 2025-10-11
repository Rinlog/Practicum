<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use \PDO;
use \DateTime;
class ApiAccessToken extends Component
{
    public $component = "";
    public $ComponentInfo;
    public $Components = [];
    public $application = "";
    public $Applications = [];
    public $ApplicationInfo;
    public $Roles;
    public $FilterUsageRoles;
    public $DisplayTableInfo;
    public $Users = [];
    public $headers = [
        "SEQ.",
        "USERNAME",
        "ROLE",
        "APPLICATION",
        "COMPONENT NAME",
        "API TOKEN",
        "CREATION TIME",
        "EXPIRY DATE",
        "CREATED BY",
        "DESCRIPTION"
    ];
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
    public function LoadSoftwareComponents(){
        try{
            $this->Components = Cache::get("software_component", collect())->where("component_has_api",true)->values()->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setDefaultComponent(){
        try{
            $this->component = $this->Components[0]->component_name;
            $this->ComponentInfo = $this->Components[0];
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function setComponent($componentID){
        try{
           foreach($this->Components as $component){
                if($component->component_id == $componentID){
                    $this->component = $component->component_name;
                    $this->ComponentInfo = $component;
                }
            }
            $this->LoadRoles();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function LoadApplications(){
        try{
            $userRoles = Cache::get("user_role_association", collect())
                    ->where("user_id", session("User")->user_id);

            $ApplicationsArray = $userRoles->pluck("application_id")->all();
            if (count($ApplicationsArray) > 0){
                $this->Applications = Cache::get("application", collect())
                    ->whereIn("application_id", $ApplicationsArray);
            }
        }
        catch(Exception $e){

        }
    }
    public function setDefaultApplication(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session()->get("User")) {
            try{
               
                $this->application = $this->Applications[0]->application_name;
                $this->ApplicationInfo = $this->Applications[0];
            }
            catch(Exception $e){
                $this->application = "";
            }
        }
    }
    public function SetApplication($NewOrgID){
        foreach ($this->Applications as $Application){
            if ($Application->application_id == $NewOrgID) {
                $this->ApplicationInfo = $Application;
                $this->application = $Application->application_name;
            }
        }
    }
    public function LoadRoles(){
        try{
            $RawRole = Cache::get("role", collect());
            $this->Roles = $RawRole->where("component_id", $this->ComponentInfo->component_id)->values()->toArray();
            $this->FilterUsageRoles = $RawRole
            ->where("component_id", $this->ComponentInfo->component_id)
            ->pluck("role_id")
            ->values()
            ->toArray();
        }
        catch(Exception $e){

        }
    }
    public function LoadUsers(){
        try{
            $this->Users = Cache::get("users", collect())->toArray();
        }
        catch(Exception $e){

        }
    }
    public function SearchForRole($roleID){
        try{
            foreach ($this->Roles as $Role){
                if ($Role->role_id == $roleID){
                    return $Role;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForRoleByName($roleName){
        try{
            foreach ($this->Roles as $Role){
                if ($Role->role_name == $roleName){
                    return $Role;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForUser($userID){
        try{
            foreach ($this->Users as $User){
                if ($User->user_id == $userID){
                    return $User;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function SearchForUserByName($userName){
        try{
            foreach ($this->Users as $User){
                if ($User->user_username == $userName){
                    return $User;
                }
            }
        }
        catch(Exception $e){

        }
    }
    public function LoadInfo(){
        try{
            $sql = "SELECT user_id,role_id,assoc_creation_time,assoc_created_by,assoc_desc,assoc_expiry_date,assoc_api_token 
            from user_role_association
            where role_id in (:validRoleIds)
            and application_id = :appId";
            
            $stmnt = $this->conn->prepare($sql);
            $stmnt->execute([
                ":validRoleIds"=>implode(", ",$this->FilterUsageRoles),
                ":appId"=>$this->ApplicationInfo->application_id
            ]);
            $Info = $stmnt->fetchAll(PDO::FETCH_OBJ);

            $this->DisplayTableInfo = "";
                foreach ($Info as $key => $val) {
                    $User = $this->SearchForUser($val->user_id);
                    $Role = $this->SearchForRole($val->role_id);
                    $TRID = $val->user_id;
                    $RawID = $val->user_id;
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$RawID}')\">
                        </td>
                        <td></td>
                        <td>{$User->user_username}</td>
                        <td>{$Role->role_name}</td>
                        <td>{$this->application}</td>
                        <td>{$this->component}</td>
                        <td>{$val->assoc_api_token}</td>
                        <td>{$val->assoc_creation_time}</td>
                        <td>{$val->assoc_expiry_date}</td>
                        <td>{$val->assoc_created_by}</td>
                        <td>{$val->assoc_desc}</td>
                    </tr>";
                }
        }
        catch(Exception $e){
            $this->DisplayTableInfo = "";
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SaveToDb($actions){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(session()->get("User"))) { return null; }
        $ArrayOfActions = json_decode($actions, true);
        $Results = [];
        foreach ($ArrayOfActions as $action){
            $ActionSplit = explode("~!~", $action);
            $Query = $ActionSplit[0];
            $Value = $ActionSplit[1];
            if (str_contains(strtolower($Query),"update")) {
                $Object = json_decode($Value);
                if (strtolower($Object->{"API TOKEN"}) == "will generate automatically"){
                    $User = $this->SearchForUserByName($Object->{"USERNAME"});
                    $Role = $this->SearchForRoleByName($Object->{"ROLE"});
                    $PreEncrypted = $User->user_id .", " .$this->ApplicationInfo->application_id .", " . $Role->role_id .", " . (new DateTime())->format("D M d Y H:i:s T P") ;
                    $Encrypted = $this->EncryptBasedOnSalt($PreEncrypted,$User->user_salt);
                }
                try{
                    $result = DB::table("user_role_association")
                    ->where("user_id",$User->user_id)
                    ->where("role_id",$Role->role_id)
                    ->where("application_id",$this->ApplicationInfo->application_id)
                    ->update([
                        "assoc_api_token"=>$Encrypted
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> session()->get("User")->user_username,
                        "log_activity_desc"=>"Updated api token to ". $Object->{"API TOKEN"}
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    Log::channel("customlog")->info($e);
                    array_push($Results, false);
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("user_role_association")->whereIn("user_id", $ItemsToDelete)->update([
                        "assoc_api_token"=>""
                    ]);

                    DB::transaction(function() use ($ItemsToDelete){
                        foreach ($ItemsToDelete as $Item){
                            DB::table("log")->insert([
                                "log_activity_time"=>now(),
                                "log_activity_type"=>"UPDATE",
                                "log_activity_performed_by"=> session()->get("User")->user_username,
                                "log_activity_desc"=>"removed api token from user ". $Item
                            ]);
                        }
                    });
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("user_role_association");
        Cache::rememberForever("user_role_association", fn() => DB::table("user_role_association")->get());
        return $Results;
    }
    public function LogExport(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(session()->get("User"))) { return null; }
        DB::table("log")->insert([
            "log_activity_time"=>now(),
            "log_activity_type"=>"REPORT",
            "log_activity_performed_by"=> session()->get("User")->user_username,
            "log_activity_desc"=>"Downloaded CSV of api access Info"
        ]);
    }
    public function EncryptBasedOnSalt($ToEncrypt, $Salt){
        $stmt = $this->conn2->prepare("SELECT key_data FROM key_vault WHERE key_id = :id");
        $stmt->execute([":id"=>$Salt]);
        $iv_keyRaw = $stmt->fetchColumn();

        $iv_key = explode(",",$iv_keyRaw);
        $key2 = base64_decode($iv_key[1]);
        $iv_decoded = base64_decode($iv_key[0]);
        $encrypted = openssl_encrypt(
            $ToEncrypt,
            'AES-256-CBC',
            $key2,
            OPENSSL_RAW_DATA,
            $iv_decoded
            
        );
        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }
    public function DecryptMessage($Message, $Username){
        if (strtolower($Message) == "will generate automatically"){
            return false;
        }
        $User = $this->SearchForUserByName($Username);
        $stmt = $this->conn2->prepare("SELECT key_data FROM key_vault WHERE key_id = :id");
        $stmt->execute([":id"=>$User->user_salt]);
        $iv_keyRaw = $stmt->fetchColumn();

        $iv_key = explode(",",$iv_keyRaw);
        if (count($iv_key) == 2){
            $iv = base64_decode($iv_key[0]);
            $key = base64_decode($iv_key[1]);
            $DecodedMessage = base64_decode($Message);

            $decrypted = openssl_decrypt(
                $DecodedMessage,
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
    public $Perms = [
        "create"=>false,
        "read"=>false,
        "update"=>false,
        "delete"=>false,
        "report"=>false
    ];
    public function LoadPagePerms(){
        try{
            $PermsDetailed = session()->get("settings-api access token info");
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
        $this->LoadUsers();
        return view('livewire.settings.api-access-token');
    }
}
