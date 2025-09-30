<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use \Exception;
use \DateTime;
use Illuminate\Support\Facades\Artisan;
class UserInfo extends Component
{
     public $headers = [
        "SEQ.",
        "ORGANIZATION",
        "USER ID",
        "USER NAME",
        "NAME",
        "PHONE NUMBER",
        "EMAIL",
        "IS SUPER ADMIN",
        "IS DISABLED",
        "CREATION TIME",
        "CREATED BY"
    ];
    public $organization = "";
    public $Organizations = [];
    public $OrgInfo;
    public $DisplayTableInfo = "";
    public $user;
    public function LoadUsersOrganization(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = collect(Cache::get('organization', collect()))
                    ->firstWhere("organization_id", $_SESSION["User"]->organization_id);
                $this->organization = $organizationInfo->organization_name;
                $this->OrgInfo = $organizationInfo;
            }
            catch(Exception $e){
                $this->organization = "";
            }
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
    public function LoadOrganizations(){
        try{
            $this->Organizations = Cache::get("organization",collect())->toArray();
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SetOrg($NewOrgID){
        $NewOrg = [];
        foreach ($this->Organizations as $org){
            if ($org->organization_id == $NewOrgID) {
                $NewOrg = $org;
            }
        }
        $this->OrgInfo = $NewOrg;
        $this->organization = $NewOrg->organization_name;
    }
    public function LoadInfo(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["User"])) {
            try{
                $UserInfo = Cache::get("users", collect())
                ->where("organization_id", $this->OrgInfo->organization_id);
                $this->DisplayTableInfo = "";
                foreach ($UserInfo as $key => $user) {
                    $TRID = $this->SpaceToUnderScore($user->user_username);
                    $IsSuperAdmin = $user->user_is_super_admin ? 'true' : 'false'; 
                    $userIsDisabled = $user->user_is_disabled ? 'true' : 'false'; 
                    $this->DisplayTableInfo.=
                    "<tr id={$TRID}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.ItemChecked(\$event,'{$user->user_username}')\">
                        </td>
                        <td></td>
                        <td>{$this->organization}</td>
                        <td>{$user->user_id}</td>
                        <td>{$user->user_username}</td>
                        <td>{$user->user_name}</td>
                        <td>{$user->user_email}</td>
                        <td>{$user->user_phone}</td>
                        <td>{$IsSuperAdmin}</td>
                        <td>{$userIsDisabled}</td>
                        <td>{$user->user_creation_time}</td>
                        <td>{$user->user_created_by}</td>
                    </tr>";
                }
            }
            catch(Exception $e){
                Log::channel("customlog")->error($e->getMessage());
            }
        }
    }
    public function SpaceToUnderScore($input){
        try{
            $input = str_replace(" ","_", $input);
            return $input;
        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
        }
    }
    public function SaveToDb($actions){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION["User"]))) { return null; }
        $ArrayOfActions = json_decode($actions, true);
        $Results = [];
        foreach ($ArrayOfActions as $action){
            $ActionSplit = explode("~!~", $action);
            $Query = $ActionSplit[0];
            $Value = $ActionSplit[1];
            $organizationID = $this->OrgInfo->organization_id;
            if (str_contains(strtolower($Query),"insert")) {
                $Object = json_decode($Value);
                try{
                    if (strtolower($Object->{"USER ID"}) == "will generate automatically"){
                        $Object->{"USER ID"} = Uuid::uuid4()->toString();
                    }
                    $PassInfo = $this->GenEncryptedPass("idl123abc");
                    $Salt = $this->GenSalt($Object->{"USER NAME"}, (new DateTime())->format("D M d Y H:i:s T P"));
                    $keystore = DB::connection("pgsql_2")->table("key_vault")->insert([
                        "key_id"=>$Salt,
                        "key_data"=>$PassInfo[0] . ',' . $PassInfo[1]
                    ]);
                    $result = DB::table("users")->insert([
                        "user_id"=>$Object->{"USER ID"},
                        "organization_id"=> $organizationID,
                        "user_username" => $Object->{"USER NAME"},
                        "user_password"=> $PassInfo[2],
                        "user_salt"=> $Salt,
                        "user_name"=> $Object->{"NAME"},
                        "user_email"=> $Object->{"EMAIL"},
                        "user_phone"=> $Object->{"PHONE NUMBER"},
                        "user_is_super_admin"=> $Object->{"IS SUPER ADMIN"},
                        "user_is_disabled"=> $Object->{"IS DISABLED"},
                        "user_creation_time"=> now(),
                        "user_created_by"=> $Object->{"CREATED BY"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"INSERT",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Inserted user ". $Object->{"USER ID"}
                    ]);
                    if ($keystore == true && $result == true){
                        array_push($Results, $result);
                    }
                    else{
                        array_push($Results, false);
                    }
                }
                catch(Exception $e){
                    array_push($Results, false);
                    log::channel("customlog")->error($e->getMessage());
                }
            }
            else if (str_contains(strtolower($Query),"delete")){
                $ItemsToDelete = explode(",",$Value);
                try{
                    $result = DB::table("users")->whereIn("user_username", $ItemsToDelete)->delete();

                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"DELETE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Deleted user(s): ". $Value
                    ]);
                    array_push($Results, $result);
                }
                catch(Exception $e){
                    array_push($Results, 0);
                }
            }
            else if (str_contains(strtolower($Query),"update")){
                try{
                    $Object = json_decode($Value);
                    $bracketloc = strpos($Query,"[");
                    //subtracts the position of the opening bracket (not including the open bracket) plus 1 more for the end bracket
                    $idToUpdate = substr($Query,$bracketloc+1,strlen($Query)-($bracketloc+2));

                    if (strtolower($Object->{"USER ID"}) == "will generate automatically"){
                        $Object->{"USER ID"} = DB::table("users")->where("user_username", $idToUpdate)->value("user_id");
                    }
                    $ResetPass = $ActionSplit[2];
                    $resetPassResult = 1;
                    if ($ResetPass == "true"){
                        $PasswordInfo = $this->GenEncryptedPass("idl123abc");

                        $Salt = Cache::get("users", collect())->where("user_id",$Object->{"USER ID"})->pluck("user_salt");
                        //check if salt exists
                        $SaltFound = DB::connection("pgsql_2")->table("key_vault")->where("key_id",$Salt)->exists();
                        if ($SaltFound == true){
                            $keystore = DB::connection("pgsql_2")->table("key_vault")->where("key_id",$Salt)->update([
                                "key_data"=>$PasswordInfo[0] . ',' . $PasswordInfo[1]
                            ]);
                        }
                        else{
                            $keystore = DB::connection("pgsql_2")->table("key_vault")->insert([
                                "key_id"=>$Salt,
                                "key_data"=>$PasswordInfo[0] . ',' . $PasswordInfo[1]
                            ]);
                        }
                        $resetPassResult = DB::table("users")->where("user_username",$idToUpdate)->update([
                            "user_password"=> $PasswordInfo[2]
                        ]);
                    }
                    $result = DB::table("users")->where("user_username", $idToUpdate)->update([
                        "user_username" => $Object->{"USER NAME"},
                        "user_name"=> $Object->{"NAME"},
                        "user_email"=> $Object->{"EMAIL"},
                        "user_phone"=> $Object->{"PHONE NUMBER"},
                        "user_is_super_admin"=> $Object->{"IS SUPER ADMIN"},
                        "user_is_disabled"=> $Object->{"IS DISABLED"},
                    ]);
                    DB::table("log")->insert([
                        "log_activity_time"=>now(),
                        "log_activity_type"=>"UPDATE",
                        "log_activity_performed_by"=> $_SESSION["User"]->user_username,
                        "log_activity_desc"=>"Updated user ". $Object->{"USER ID"}
                    ]);
                    if ($result == 1 && $resetPassResult == 1){
                        array_push($Results, $result);
                    }
                    else{
                        array_push($Results, 0);
                    }
                }
                catch(Exception $e){
                    Log::channel("customlog")->error($e->getMessage());
                    array_push($Results, 0);
                }
            }
        }
        Cache::forget("users");
        Cache::rememberForever("users", fn() => DB::table("users")->get());
        return $Results;
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
            "log_activity_desc"=>"Downloaded CSV of user Info"
        ]);
    }
    public function render()
    {
        if (!(Cache::has("users"))){
            Artisan::call("precache:tables");
        }
        $this->LoadUserInfo();
        return view('livewire.settings.user-info');
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
    public function GenSalt($Username,$DateCreated){
        $UnhexedSalt = $DateCreated . $Username;
        $HexSalt = bin2hex($UnhexedSalt);
        return $HexSalt;
    }
}
