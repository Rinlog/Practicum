<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
class Navigation extends Component
{
    public $width = "w-16";
    public $buttonPos = "left-10";
    public $buttonText = ">>";
    public $CurrentPage = "";
    //hashmaps of all the pages including there labels and routes 
    //would want some of these things associated in an automatically updating config file (or another solution for the problem)
    //but time constraints required this solution.
    //keep in mind currently if a resource name or sub name changes permissions will not function properly until it is also updated here.
    public $settings = [
        'device info' => ['Devices', 'deviceInfo'],
        'device type info' => ['Device Types', 'deviceTypeInfo'],
        'sensor type info' => ['Sensor Types', 'sensorTypeInfo'],
        'sensor info' => ['Sensors', 'sensorInfo'],
        'sensor data types info' => ['Sensor Data Types', 'sensorDataTypeInfo'],
        'location info' => ['Locations', 'locationInfo'],
        'sub-location info' => ['Sub-Locations', 'subLocationInfo'],
        'application info' => ['Applications', 'applicationInfo'],
        'organization info' => ['Organizations', 'organizationInfo'],
        'software component info' => ['Software Components', 'softwareComponentInfo'],
        'resource info' => ['Resources', 'resourceInfo'],
        'permission info' => ['Permissions', 'permissionInfo'],
        'role info' => ['Roles', 'roleInfo'],
        'user info' => ['Users', 'userInfo'],
        'api access token info' => ['API Access Token', 'apiAccessToken'],
    ];

    public $associations = [
        'application-sensor type association' => ['Application-Sensor Type', 'applicationSensorTypeAssociation'],
        'application-device type association' => ['Application-Device Type', 'applicationDeviceTypeAssociation'],
        'application-device association' => ['Application-Device', 'applicationDeviceAssociation'],
        'application-location association' => ['Application-Location', 'applicationLocationAssociation'],
        'device-sensor association' => ['Device-Sensor', 'deviceSensorAssociation'],
        'role-permission association' => ['Role-Permission', 'rolePermissionAssociation'],
        'user-role association' => ['User-Role', 'userRoleAssociation'],
        'sensor-data types association' => ['Sensor-Data Types', 'sensorDataTypeAssociation'],
    ];

    public $deployment = [
        'device deployment' => ['Device Deployment', 'deviceDeployement'],
    ];
    public function render()
    {   
        $this->LoadPerms();
        return view('livewire.navigation');
    }
    public $ShowSensorReadings = false;
    public $ShowReadings = false;
    public $ShowLogs = false;
    public $ShowSettings = false;
    public $SettingPages = []; //holds all permissions associated with settings
    function ForgetKeys(){
        session()->forget([
            "settings-device info",
            "settings-device type info",
            "settings-sensor type info",
            "settings-sensor info",
            "settings-sensor data types info",
            "settings-location info",
            "settings-sub-location info",
            "settings-application info",
            "settings-organization info",
            "settings-software component info",
            "settings-resource info",
            "settings-permission info",
            "settings-role info",
            "settings-user info",
            "settings-api access token info",
            "settings-application-sensor type association",
            "settings-application-device type association",
            "settings-application-device association",
            "settings-application-location association",
            "settings-device-sensor association",
            "settings-role-permission association",
            "settings-user-role association",
            "settings-sensor-data types association",
            "settings-device deployment",
            "logs-general log",
            "logs-application-specific log",
            "browse_sensor_readings-sensor readings",
            "browse_sensor_readings-hourly averages",
            "browse_sensor_readings-daily averages",
            "browse_readings-device readings",
            "browse_readings-sensor readings"
        ]);
    }
    function LoadPerms(){
        if (session()->get("IsSuperAdmin") == true){
                $this->ShowSettings = true;
                $this->ShowLogs = true;
                $this->ShowReadings = true;
                $this->ShowSensorReadings = true;
            }
        $Permissions = session()->get("AllAppPermsForUser");
        $ComponenentOfInterest = session()->get("AdminComponentID");
        $this->ForgetKeys();
        if ($Permissions == null){
            return;
        }
        foreach ($Permissions as $Permission){
            if ($Permission->component_id == $ComponenentOfInterest){
                if (strtolower($Permission->resource_name) == "settings"){
                    $this->ShowSettings = true;
                    //we are adding all sub-resource permissions to there own session storage
                    $key = strtolower($Permission->resource_sub_name);
                    $array = session()->get($key, []);
                    $array[] = $Permission;
                    session()->put("settings-".$key, $array);
                }
                else if (strtolower($Permission->resource_name) == "logs"){
                    $this->ShowLogs = true;
                    $key = strtolower($Permission->resource_sub_name);
                    $array = session()->get($key, []);
                    $array[] = $Permission;
                    session()->put("logs-" . $key, $array);
                }
                else if (strtolower($Permission->resource_name) == "browse readings"){
                    $this->ShowReadings = true;
                    $key = strtolower($Permission->resource_sub_name);
                    $array = session()->get($key, []);
                    $array[] = $Permission;
                    session()->put("browse_readings-" . $key, $array);
                }
                else if (strtolower($Permission->resource_name) == "browse sensor readings"){ //using browse sensor readings to display dashboard.
                    $this->ShowSensorReadings = true;
                    $key = strtolower($Permission->resource_sub_name);
                    $array = session()->get($key, []);
                    $array[] = $Permission;
                    session()->put("browse_sensor_readings-" . $key, $array);
                }
            }
        }
    }

    public function ChangeSize($width){
        if ($width == 'w-16'){
            $this->width = "w-64";
            $this->buttonPos = "left-57";
            $this->buttonText = "<<";
        }
        else if ($width == "w-64"){
            $this->width = "w-16";
            $this->buttonPos = "left-10";
            $this->buttonText = ">>";
        }
    }
}
