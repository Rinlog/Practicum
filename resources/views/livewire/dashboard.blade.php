
<div class="bg-[#f2f2f2] flex flex-row">
        @if (empty(session()->get("AllAppPermsForUser"))))
            <script>window.location = "/";</script>
        @endif
        <livewire:navigation></livewire:navigation>
        @vite(['resources/js/ComponentJS/FilterJS.js'])
        {{-- main section --}}
        <div class="w-screen flex flex-col flex-wrap max-h-[895px]">
            <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
            <div class="lg:p-10 md:p-10 pb-10 pr-10 pl-2 pt-2 rounded-rlb-lg h-screen w-[100%] md:w-[80%] lg:w-[82%]">
                <div id="InfoSection" class="flex flex-col lg:flex-row lg:w-full gap-0 lg:overflow-y-hidden ">
                    <div class="relative w-full lg:flex-1 pt-5 overflow-y-hidden overflow-x-hidden">
                        {{-- info selection --}}
                        <div class="flex">
                            <div class="relative inline-block text-left w-full lg:pr-4 lg:pr-0 bg-[#f6f6f6] rounded-t-lg">
                                <span class="flex flex-row flex-grow">
                                    <ul class="flex h-full lg:flex-row flex-col w-full lg:w-auto">
                                        @php
                                            // Define the available tabs dynamically based on session permissions
                                            $tabs = [];

                                            if (session('browse_sensor_readings-sensor readings')) {
                                                $tabs['allSensorReadings'] = [
                                                    'label' => 'Sensor Readings',
                                                    'click' => 'SwitchToAllReadings',
                                                ];
                                            }

                                            if (session('browse_sensor_readings-daily averages')) {
                                                $tabs['dailySensorReadings'] = [
                                                    'label' => 'Average Daily Readings',
                                                    'click' => 'SwitchToDailyReadings',
                                                ];
                                            }

                                            if (session('browse_sensor_readings-hourly averages')) {
                                                $tabs['hourlySensorReadings'] = [
                                                    'label' => 'Average Hourly Readings',
                                                    'click' => 'SwitchToHourlyReadings',
                                                ];
                                            }
                                        @endphp

                                        @if (!empty($tabs))
                                            @foreach ($tabs as $key => $tab)
                                                @php
                                                    $isActive = $option === $key;
                                                @endphp

                                                <li wire:click="{{ $tab['click'] }}"
                                                    class="h-full w-full p-9 rounded-t-lg whitespace-nowrap cursor-pointer hover:bg-neutral-200
                                                        {{ $isActive ? 'pl-12 bg-white text-[#056c8b] font-bold' : 'bg-[#f6f6f6] text-[#707070] font-semibold' }}">
                                                    <h2>{{ $tab['label'] }}</h2>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Content --}}
                <div class="lg:p-10 md:p-10 pb-10 pr-10 pl-2 pt-2 bg-white shadow-md rounded-rlb-lg w-full">
                    @if ($option == "allSensorReadings")
                        <livewire:dashboard.all-sensor-readings></livewire:dashboard.all-sensor-readings>
                    @elseif ($option == "dailySensorReadings")
                        <livewire:dashboard.average-daily-readings></livewire:dashboard.average-daily-readings>
                    @elseif ($option == "hourlySensorReadings")
                        <livewire:dashboard.hourly-readings></livewire:dashboard.hourly-readings>
                    @endif
                </div>
            </div>
        </div>
    </div>
