<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use \Illuminate\Database\RecordNotFoundException;
use \Exception;

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

                if (password_verify("idl123abc",$user->user_password)) {
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
            DB::table("users")->where("user_username", $this->Username)->update([
                "user_password"=>(password_hash($Password, PASSWORD_DEFAULT))
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
                if (password_verify($this->Password , $user->user_password)){
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
}