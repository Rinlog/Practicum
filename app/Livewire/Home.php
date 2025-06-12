<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use \Exception;
#[Title("Home | IDL")]
class Home extends Component
{
    public $user;
    public $userRoles = [];
    public function LoadUsersRoles(){
        try{
            $roleAssoc = DB::table("user_role_association")->where("user_id",$this->user->user_id)->get();
            $roleIds = [];
            foreach ($roleAssoc as $role){
                array_push($roleIds,$role->role_id);
            }
            $this->userRoles = DB::table("role")->whereIn("role_id",$roleIds)->get();

        }
        catch(Exception $e){
            Log::channel("customlog")->error($e->getMessage());
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
    public function render()
    {
        $this->LoadUserInfo();
        $this->LoadUsersRoles();
        return view('livewire.home');
    }
}
