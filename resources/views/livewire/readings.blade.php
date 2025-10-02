<?php session_start()?>
<div class="bg-[#f2f2f2] flex flex-row">
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    @vite(['resources/js/ComponentJS/FilterJS.js'])
    @vite(['resources/js/ComponentJS/deviceList.js'])
    @vite(['resources/js/ComponentJS/sensorList.js'])
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div id="InfoSection" class="flex flex-col lg:flex-row lg:w-[1750px] gap-0 lg:overflow-y-hidden ">
            <div class="relative w-full lg:flex-1 pl-10 pr-10 pt-5 overflow-y-hidden overflow-x-hidden">
                {{-- info selection --}}
                <div class="flex">
                    <div class="relative inline-block text-left w-full pr-4 lg:pr-0 bg-[#f6f6f6] rounded-t-lg">
                        <span class="flex flex-row flex-grow">
                            <ul class="flex h-full">
                                @if ($readingPage == "deviceReadings")
                                    <li wire:click="$js.SwitchToDeviceReading" class="bg-white h-full w-full p-9 pr-12 rounded-t-lg whitespace-nowrap text-[#056c8b] font-bold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToDeviceReading">Device</h2></li>
                                    <li wire:click="$js.SwitchToSensorReading" class="bg-[#f6f6f6] h-full w-full p-9 rounded-t-lg whitespace-nowrap text-[#707070] font-semibold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToSensorReading">Sensor</h2></li>
                                @else
                                    <li wire:click="$js.SwitchToDeviceReading" class="bg-[#f6f6f6] h-full w-full p-9 rounded-t-lg whitespace-nowrap text-[#707070] font-semibold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToDeviceReading">Device</h2></li>
                                    <li wire:click="$js.SwitchToSensorReading" class="bg-white h-full w-full p-9 pl-12 rounded-t-lg whitespace-nowrap text-[#056c8b] font-bold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToSensorReading">Sensor</h2></li>
                                @endif
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @if ($readingPage == "deviceReadings")
            <livewire:readings.device-readings></livewire:readings.device-readings>
        @elseif ($readingPage == "sensorPage")
            <livewire:readings.sensor-readings></livewire:readings.sensor-readings>
        @endif
    </div>
</div>
@script
<script>
    if (0){}

    $js("SwitchToDeviceReading",function(){
        $wire.call("ShowDeviceReadings")
    })
    $js("SwitchToSensorReading",function(){
        $wire.call("ShowSensorReadings")
    })

</script>
@endscript