<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use \Exception;
class Deviceinfo extends Component
{
    public $headers = [
        "SEQ.",
        "DEVICE EUI",
        "DEVICE NAME EUI",
        "TYPE",
        "ORGANIZATION",
        "MODEL",
        "SERIAL NO.",
        "MANUFACTURER",
        "MANUFACTURE DATE",
        "MIN SAMPLING RATE",
        "MAX SAMPLING RATE",
        "MEMORY SIZE",
        "COMMUNICATION PROTOCOL",
        "INTERACTION TYPE",
        "DETECTION TYPE",
        "OUTPUT TYPE",
        "ENCODING METHOD",
        "REQUEST METHOD",
        "DESCRIPTION",
        "IS DEPLOYED"
    ];
    public $organization = "";
    private $userOrgInfo;

    public $devices = "";
    public function LoadUsersOrganization(){
        if (isset($_SESSION["User"])) {
            try{
                $organizationInfo = DB::table("organization")->where("organization_id", $_SESSION["User"]->organization_id)->firstOrFail();
                $this->organization = $organizationInfo->organization_name;
                $this->userOrgInfo = $organizationInfo;
            }
            catch(Exception $e){
                $this->organization = "";
            }
        }
    }
    public function LoadDeviceInfo(){
        if (isset($_SESSION["User"])) {
            try{
                $deviceInfo = DB::table("device")->where("organization_id", $this->userOrgInfo->organization_id)->get();
                $this->devices = "";
                foreach ($deviceInfo as $key => $device) {
                    $deviceDeployed = $device->device_is_deployed ? 'true' : 'false';
                    $this->devices.=
                    "<tr id={$device->device_eui}>
                        <td>
                        <input type='checkbox' wire:click=\"\$js.DeviceChecked(\$event,'{$device->device_eui}')\">
                        </td>
                        <td></td>
                        <td>{$device->device_eui}</td>
                        <td>{$device->device_name}</td>
                        <td>{$device->device_type}</td>
                        <td>{$this->organization}</td>
                        <td>{$device->device_model}</td>
                        <td>{$device->device_serial_no}</td>
                        <td>{$device->device_manufacturer}</td>
                        <td>{$device->device_manufacture_date}</td>
                        <td>{$device->device_min_sampling_rate}</td>
                        <td>{$device->device_max_sampling_rate}</td>
                        <td>{$device->device_memory_size}</td>
                        <td>{$device->device_communication_protocol}</td>
                        <td>{$device->device_interaction_type}</td>
                        <td>{$device->device_detection_type}</td>
                        <td>{$device->device_output_type}</td>
                        <td>{$device->device_encoding_method}</td>
                        <td>{$device->device_request_method}</td>
                        <td>{$device->device_desc}</td>
                        <td>{$deviceDeployed}</td>
                    </tr>";
                }
            }
            catch(Exception $e){

            }
        }
    }
    public function render()
    {
        $this->LoadUsersOrganization();
        $this->LoadDeviceInfo();
        return view('livewire.settings.deviceinfo');
    }
}
