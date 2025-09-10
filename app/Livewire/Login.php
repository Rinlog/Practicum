<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use \Illuminate\Database\RecordNotFoundException;
use \Exception;
use Ramsey\Uuid\Uuid;

#[Title("Login | IDL")]
class Login extends Component
{
    public function render()
    {
        return view('livewire.login');
    }
    public $Username = "";

    public $Password = "";

    #variables required to display a modal
    #public $open = "hide";
    #public $message = "Placeholder";
    #public $title = "Title";
    #public $ButtonText = "OK";

    #three vars for one error
    private $errStyle = "border-2 border-red-500";
    public $usrCustomStyle = "";
    public $usrShowErr = "hide";
    public $usrErrMsg = "";

    public $passCustomStyle = "";
    public $passShowErr = "hide";
    public $passErrMsg = "";
    public function setOpen($open){
        
    }
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
                $user = DB::table("users")->where("user_username", $this->Username)->firstOrFail();
                $UsersPass = $this->DecryptPass($user->user_password, $user->user_salt);
                
                if ("idl123abc" == $UsersPass) {
                    if ($this->Password == "idl123abc"){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
        }
        catch (RecordNotFoundException $e) {
            return false;
        }
        catch(Exception $e){
            return false;
        }
    }
    public function ChangePass($Password){
        try{
            $PasswordInfo = $this->GenEncryptedPass($Password);

            $Salt = DB::table("users")->where("user_username",$this->Username)->value("user_salt");

            $keystore = DB::connection("pgsql_2")->table("key_vault")->where("key_id",$Salt)->update([
                "key_data"=>$PasswordInfo[0] . ',' . $PasswordInfo[1]
            ]);

            DB::table("users")->where("user_username", $this->Username)->update([
                "user_password"=> $PasswordInfo[2]
            ]);
            $this->Password = $Password;
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
    public function login(){
        try{
            #check if blank
            if ($this->Username == "") {
                $this->usrCustomStyle = $this->errStyle;
                $this->usrShowErr = "show";
                $this->usrErrMsg = "Username can not be blank";
            }
            else{
                $this->clearusr();
            }

            if ($this->Password == ""){
                $this->passCustomStyle = $this->errStyle;
                $this->passShowErr = "show";
                $this->passErrMsg = "Password can not be blank";
            }
            else{
                $this->clearpass();
            }
            if ($this->Username != "" and $this->Password != "") {
                $user = DB::table("users")->where("user_username", $this->Username)->firstOrFail();

                $userNameValid = false;
                $passwordValid = false;
                if ($user->user_is_disabled == true){
                    $this->usrCustomStyle = $this->errStyle;
                    $this->usrShowErr = "show";
                    $this->usrErrMsg = "User account is disabled";
                    return;
                }
                else{
                    $this->clearusr();
                }
                #check to make sure login info is correct
                if (strcasecmp($this->Username,$user->user_username) == 0) {
                    $userNameValid = true;
                    $this->clearusr();
                }   
                else{
                    $this->usrCustomStyle = $this->errStyle;
                    $this->usrShowErr = "show";
                    $this->usrErrMsg = "Incorrect username";
                }
                if ($this->Password == $this->DecryptPass($user->user_password,$user->user_salt)){
                    $passwordValid = true;
                    $this->clearpass();
                }
                else{
                    $this->passCustomStyle = $this->errStyle;
                    $this->passShowErr = "show";
                    $this->passErrMsg = "Incorrect password";
                }
                if ($userNameValid and $passwordValid) {
                    $this->clear();
                    session_start();
                    $_SESSION["UserName"] = ucfirst(strtolower($this->Username));
                    $_SESSION["User"] = $user;
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
    
}