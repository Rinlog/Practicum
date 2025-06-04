<?php session_start();?>
<div class="bg-[#f2f2f2] flex flex-row overflow-x-hidden block">
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div class="lg:pb-10 lg:pl-10 md:pb-10 md:pl-10 pb-15 pt-4 lg:h-[857px] w-full overflow-y-hidden">
            <h1 class="text-[#7e7e7e] pb-5">Settings</h1>
            {{-- main info --}}
            @if ($settingToDisplay == "deviceInfo")
                <livewire:settings.deviceinfo></livewire:settings.deviceinfo>
            @elseif ($settingToDisplay == "organizationInfo")
                <livewire:settings.organization-info></livewire:settings.organization-info>
            @elseif ($settingToDisplay == "applicationInfo")
                <livewire:settings.application-info></livewire:settings.application-info>
            @elseif ($settingToDisplay == "sensorTypeInfo")
                <livewire:settings.sensor-type-info></livewire:settings.sensor-type-info>
            @elseif ($settingToDisplay == "sensorInfo")
                <livewire:settings.sensor-info></livewire:settings.sensor-info>
            @elseif ($settingToDisplay == "sensorDataTypeInfo")
                <livewire:settings.sensor-data-types></livewire:settings.sensor-data-types>
            @elseif ($settingToDisplay == "locationInfo")
                <livewire:settings.location-info></livewire:settings.location-info>
            @elseif ($settingToDisplay == "subLocationInfo")
                <livewire:settings.sub-location-info></livewire:settings.sub-location-info>
            @elseif ($settingToDisplay == "softwareComponentInfo")
                <livewire:settings.software-component-info></livewire:settings.software-component-info>
            @elseif ($settingToDisplay == "resourceInfo")
                <livewire:settings.resources></livewire:settings.resources>
            @elseif ($settingToDisplay == "permissionInfo")
                <livewire:settings.permission-info></livewire:settings.permission-info>
            @elseif ($settingToDisplay == "roleInfo")
                <livewire:settings.role-info></livewire:settings.role-info>
            @elseif ($settingToDisplay == "userInfo")
                <livewire:settings.user-info></livewire:settings.user-info>
            @elseif ($settingToDisplay == "applicationSensorTypeAssociation")
                <livewire:settings.application-sensor-type-association></livewire:settings.application-sensor-type-association>
            @elseif ($settingToDisplay == "applicationDeviceAssociation")
                <livewire:settings.application-device-association></livewire:settings.application-device-association>
            @elseif ($settingToDisplay == "applicationLocationAssociation")
                <livewire:settings.application-location-association></livewire:settings.application-location-association>
            @elseif ($settingToDisplay == "deviceSensorAssociation")
                <livewire:settings.device-sensor-association></livewire:settings.device-sensor-association>
            @elseif ($settingToDisplay == "rolePermissionAssociation")
                <livewire:settings.role-permission-association></livewire:settings.role-permission-association>
            @elseif ($settingToDisplay == "userRoleAssociation")
                <livewire:settings.user-role-association></livewire:settings.user-role-association>
            @elseif ($settingToDisplay == "sensorDataTypeAssociation")
                <livewire:settings.sensor-data-type-association></livewire:settings.sensor-data-type-association>
            @endif
        </div>
    </div>
</div>