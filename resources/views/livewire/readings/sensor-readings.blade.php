<div id="MainWindowLogs" class="flex flex-col lg:flex-row w-full max-w-[1750px] gap-0">
    <div class="relative w-full lg:flex-1 pl-10 pr-10 flex-grow flex-col sm:flex-row">
    <div class="lg:p-10 md:p-10 pr-10 pl-2 pt-2 bg-white shadow-md flex flex-grow flex-col gap-2 rounded-b-lg min-h-[1770px] w-full md:min-h-[781px] lg:min-h-[750px] overflow-y-hidden">
        <div class="flex">
            <div class="text-left w-full pr-4 lg:pr-0 md:pr-0 sm:flex items-center gap-2">
                {{-- Organization Selector --}}
                <div id="OrganizationSelector" class="w-[70%] flex items-center">
                    <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md">Organization</label>
                    <div class="selectWrapperLG w-full">
                        <select key={{ Str::random() }} id="Organizations" class="open-sans-soft-regular border-r-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] text-white text-lg hover:bg-[#4a4a4a] w-full p-6 pr-10 rounded-r-lg font-bold shadow-md">
                                @foreach($Organizations as $org)
                                          @if ($org->organization_id == $OrgInfo->organization_id) 
                                            <option wire:key={{ Str::random() }} selected wire:click="$js.ChangeOrg($event,'{{ $org->organization_id }}')" id="{{ $org->organization_id }}">{{ $org->organization_name }}</option>
                                        @else
                                            <option wire:key={{ Str::random() }} wire:click="$js.ChangeOrg($event,'{{ $org->organization_id }}')" id="{{ $org->organization_id }}">{{ $org->organization_name }}</option>
                                         @endif
                                @endforeach
                        </select>
                    </div>
                </div>
                {{-- Search bar --}}
                <div class="flex lg:w-full relative max-h-[56px]">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 30 30" stroke="#666666" fill="#666666" class="mt-4 absolute left-3">
                        <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                    </svg>
                    <input type="text" id="SearchBarLogs" class="bg-white p-4 rounded-lg w-[80%] border-2 border-gray-200 pl-10" placeholder="Search by Keyword">
                </div>
                {{-- refresh button --}}
                {{-- export button --}}
                <span class="flex gap-4 items-center sm:justify-end grow p-1">
                    <button wire:click="$js.refresh" class="text-[#46c0e5] text-5xl hover:bg-gray-100 rounded-lg hover:outline-hidden cursor-pointer p-1 fill-[#46c0e5] hover:fill-[#3c8fb0]">
                        <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px">
                           <path id="Refresh" class="cls-1" d="M22.96,12.07c-.25-2.66-1.52-5.07-3.58-6.78-.04-.03-.08-.06-.12-.09-.44-.27-1.01-.21-1.39.14-.23.21-.36.5-.37.81-.01.31.1.6.31.83.03.03.06.06.09.08,1.06.88,1.87,2.02,2.34,3.32.7,1.93.6,4.02-.27,5.88-.87,1.86-2.42,3.27-4.35,3.96-4,1.44-8.42-.63-9.86-4.62-.44-1.23-.57-2.55-.36-3.84.56-3.47,3.37-6.01,6.7-6.4l-1.18,1.18c-.39.39-.39,1.02,0,1.41.2.2.45.29.71.29s.51-.1.71-.29l2.77-2.77s.01,0,.02,0c.03-.02.04-.05.06-.07l.15-.15s.04-.07.07-.1c0,0,.01-.01.01-.02.29-.39.28-.94-.08-1.29l-3-3c-.39-.39-1.02-.39-1.41,0-.39.39-.39,1.02,0,1.41l1.11,1.11c-3.48.35-6.59,2.49-8.1,5.68-.62,1.31-.94,2.78-.95,4.23,0,2.67,1.03,5.19,2.92,7.08s4.4,2.94,7.07,2.94h0c2.98,0,5.79-1.32,7.69-3.61,1.71-2.06,2.51-4.65,2.27-7.31Z"/>
                        </svg>
                    </button>
                    <button id="Export" wire:click="$js.DownloadCSV" class="export flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px" class="svg" fill="#46c0e5">
                            <path id="Export" class="cls-1" d="M16.71,13.62c.39.39.39,1.02,0,1.41-.2.2-.45.29-.71.29s-.51-.1-.71-.29l-1.29-1.29v3.93c0,.55-.45,1-1,1s-1-.45-1-1v-3.93l-1.29,1.29c-.39.39-1.02.39-1.41,0s-.39-1.02,0-1.41l3-3c.38-.38,1.04-.38,1.41,0l3,3ZM23,8v12.81c-.03,2.31-1.94,4.19-4.29,4.19H7.31s-.05,0-.06,0c-2.31,0-4.22-1.88-4.25-4.22V5.19c.03-2.31,1.94-4.19,4.25-4.19h.03s8.71,0,8.71,0c.53,0,1.04.21,1.41.59l5,5c.38.38.59.88.59,1.41ZM17,7h3l-3-3v3ZM21,9h-4c-1.1,0-2-.9-2-2v-4h-7.71s-.02,0-.03,0c-1.23,0-2.24.99-2.25,2.22v15.56c.02,1.23,1.02,2.22,2.25,2.22.01,0,.02,0,.03,0h11.43s.02,0,.03,0c1.23,0,2.24-.99,2.25-2.22v-11.78Z"/>
                        </svg>
                        EXPORT
                    </button>
                </span>
            </div>
        </div>
        <span class="flex lg:flex-row lg:items-center md:flex-col flex-col lg:justify-between md:justify-between mb-4 gap-4">
            {{-- top half --}}
            {{-- device + calendar + filter --}}
            <span class="flex lg:flex-row lg:items-center flex-col gap-4 items-start lg:flex-grow md:flex-grow">
                <span id="DeviceContainer" class="relative">
                    <button id="Devices" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[420px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none">
                        <path d="M9.2255 5.33199C8.92208 5.86298 8.93352 6.58479 9.55278 6.89443C10.3201 7.27808 10.7664 6.59927 11.1323 6.04284C11.4473 5.56389 11.8013 5.11292 12.2071 4.70711C13.0981 3.8161 14.3588 3 16 3C17.6412 3 18.9019 3.8161 19.7929 4.70711C20.1967 5.11095 20.5495 5.5595 20.8632 6.03593C21.2289 6.59127 21.6809 7.27759 22.4472 6.89443C23.0634 6.58633 23.0765 5.86043 22.7745 5.33199C22.7019 5.20497 22.5962 5.02897 22.4571 4.8203C22.1799 4.40465 21.7643 3.8501 21.2071 3.29289C20.0981 2.1839 18.3588 1 16 1C13.6412 1 11.9019 2.1839 10.7929 3.29289C10.2357 3.8501 9.82005 4.40465 9.54295 4.8203C9.40383 5.02897 9.29809 5.20497 9.2255 5.33199Z" fill="#FFFFFF"/>
                        <path d="M14.4762 6.71292C14.2768 6.90911 14.1223 7.10809 14.0182 7.2579C13.6696 7.75991 13.1966 8.23817 12.5294 7.88235C11.9766 7.58751 11.8923 6.8973 12.193 6.39806C12.2358 6.327 12.2967 6.23053 12.3755 6.1171C12.5319 5.89191 12.7649 5.59089 13.0738 5.28708C13.6809 4.68987 14.6689 4 15.9999 4C17.3309 4 18.3189 4.68986 18.9261 5.28706C19.235 5.59087 19.468 5.89188 19.6244 6.11707C19.7031 6.2305 19.764 6.32697 19.8069 6.39803C20.1082 6.8983 20.0212 7.58858 19.4705 7.88233C18.8032 8.23829 18.3304 7.76001 17.9817 7.25793C17.8776 7.10812 17.7231 6.90913 17.5236 6.71294C17.1141 6.31014 16.6021 6 15.9999 6C15.3977 6 14.8857 6.31013 14.4762 6.71292Z" fill="#FFFFFF"/>
                        <path d="M5 18C4.44772 18 4 18.4477 4 19C4 19.5523 4.44772 20 5 20C5.55228 20 6 19.5523 6 19C6 18.4477 5.55228 18 5 18Z" fill="#FFFFFF"/>
                        <path d="M7 19C7 18.4477 7.44771 18 8 18C8.55229 18 9 18.4477 9 19C9 19.5523 8.55229 20 8 20C7.44771 20 7 19.5523 7 19Z" fill="#FFFFFF"/>
                        <path d="M10 19C10 18.4477 10.4477 18 11 18C11.5523 18 12 18.4477 12 19C12 19.5523 11.5523 20 11 20C10.4477 20 10 19.5523 10 19Z" fill="#FFFFFF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15 8C15 7.44771 15.4477 7 16 7C16.5523 7 17 7.44771 17 8V15H20C21.6569 15 23 16.3431 23 18V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V18C1 16.3431 2.34315 15 4 15H15V8ZM20 17C20.5523 17 21 17.4477 21 18V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V18C3 17.4477 3.44772 17 4 17H20Z" fill="#FFFFFF"/>
                        </svg>
                        <label class="cursor-pointer">{{ $device }}</label>
                        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div id="DeviceDropDown" isOpen="false" class="absolute left-0 z-3 mt-2 w-90 sm:w-110 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="p-4 flex flex-col items-center" role="none">
                           <h2 class="text-[#386d8c] font-semibold">DEVICES</h2>
                           <ul class="overflow-hidden overflow-y-scroll w-full h-50">
                            @foreach($devices as $device)
                                <li class="p-2 cursor-pointer hover:bg-[#f2f2f2] text-gray-500" id={{ $device->device_eui }} wire:click="$js.setDevice('{{ $device->device_eui }}')">{{ $device->device_name }}</li>
                            @endforeach
                           </ul>
                        </div>
                    </div>
                </span>
                <span id="SensorContainer" class="relative">
                    <button id="Sensors" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[420px]">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 512 512" version="1.1">
                            <title>radio-waves</title>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="drop" fill="#FFFFFF" transform="translate(42.666667, 78.641067)">
                                    <path d="M256,177.358933 C256,200.9216 236.898133,220.0256 213.333333,220.0256 C189.768533,220.0256 170.666667,200.9216 170.666667,177.358933 C170.666667,153.796267 189.768533,134.692267 213.333333,134.692267 C236.898133,134.692267 256,153.796267 256,177.358933 Z M128,177.358933 C128,147.776 143.071573,121.73696 165.936213,106.427093 L142.22272,70.94272 C107.92576,93.9063467 85.3333333,132.983467 85.3333333,177.358933 C85.3333333,221.7344 107.92576,260.8128 142.22272,283.776 L165.936213,248.288 C143.071573,232.9792 128,206.941867 128,177.358933 Z M298.666667,177.358933 C298.666667,206.941867 283.594667,232.9792 260.7296,248.288 L284.443733,283.776 C318.741333,260.8128 341.333333,221.7344 341.333333,177.358933 C341.333333,132.983467 318.741333,93.9061333 284.443733,70.94272 L260.7296,106.427093 C283.594667,121.73696 298.666667,147.776 298.666667,177.358933 Z M331.850667,1.42108547e-14 L308.1472,35.47136 C353.8752,66.0885333 384,118.195413 384,177.358933 C384,236.522667 353.8752,288.629333 308.1472,319.246933 L331.850667,354.717867 C389.009067,316.448 426.666667,251.3152 426.666667,177.358933 C426.666667,103.403733 389.0112,38.2709333 331.850667,1.42108547e-14 Z M94.8164267,354.717867 L118.519467,319.249067 C72.7904,288.629333 42.6666667,236.522667 42.6666667,177.358933 C42.6666667,118.195413 72.7904,66.0885333 118.519467,35.4688 L94.8164267,1.42108547e-14 C37.65632,38.2709333 7.10542736e-15,103.40096 7.10542736e-15,177.358933 C7.10542736e-15,251.317333 37.65632,316.448 94.8164267,354.717867 Z" id="Shape">

                        </path>
                                </g>
                            </g>
                        </svg>
                        <label class="cursor-pointer">{{ $sensor }}</label>
                        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div id="SensorDropDown" isOpen="false" class="absolute left-0 z-3 mt-2 w-90 sm:w-110 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="p-4 flex flex-col items-center" role="none">
                           <h2 class="text-[#386d8c] font-semibold">SENSORS</h2>
                           <ul class="overflow-hidden overflow-y-scroll w-full h-50">
                            @foreach($sensors as $sensor)
                                <li class="p-2 cursor-pointer hover:bg-[#f2f2f2] text-gray-500" id={{ $sensor->sensor_id }} wire:click="$js.setSensor('{{ $sensor->sensor_id }}')">{{ $sensor->sensor_name }}</li>
                            @endforeach
                           </ul>
                        </div>
                    </div>
                </span>
                <button id="DateRangePicker" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[290px]">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" viewBox="0 0 26 26" class="size-5 min-h-[26px] min-w-[26px]">
                        <path id="Calendar" class="cls-1" d="M20.5,3h-1.5v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-8v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-1.5c-1.93,0-3.5,1.57-3.5,3.5v15c0,1.93,1.57,3.5,3.5,3.5h15c1.93,0,3.5-1.57,3.5-3.5V6.5c0-1.93-1.57-3.5-3.5-3.5ZM5.5,5h1.5v2c0,.55.45,1,1,1s1-.45,1-1v-2h8v2c0,.55.45,1,1,1s1-.45,1-1v-2h1.5c.83,0,1.5.67,1.5,1.5v4H4v-4c0-.83.67-1.5,1.5-1.5ZM20.5,23H5.5c-.83,0-1.5-.67-1.5-1.5v-9h18v9c0,.83-.67,1.5-1.5,1.5Z"/>
                    </svg>
                    <label id="DateRangeText" class="cursor-pointer">{{ $TimeFrame }}</label>
                    <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <span id="FilterContainer" class="relative">                
                    <button id="Filter" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[290px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none">
                            <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.24 16.24L12 12" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <label class="cursor-pointer">{{$StartTime}} - {{ $EndTime }}</label>
                        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div id="FilterDropDown" isOpen="false" class="absolute left-0 z-3 mt-2 w-90 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="p-4 flex flex-col items-center" role="none">
                            <livewire:components.req-underline-input id="startTime" text="Start Time*" textColor="text-gray-500" inputColor="text-gray-600" type="time"></livewire:components.req-underline-input>
                            <livewire:components.req-underline-input id="endTime" text="End Time*" textColor="text-gray-500" inputColor="text-gray-600" type="time"></livewire:components.req-underline-input>
                        </div>
                        <span class="flex flex-row justify-center items-center p-4">
                            <button wire:click="$js.Filter" class="bg-white border-2 border-[#46c0e5] p-3 rounded-full text-[#46c0e5] font-semibold hover:text-[#3c8fb0] hover:border-[#3c8fb0] cursor-pointer w-full">
                                CONFIRM
                            </button>
                        </span>
                    </div>
                </span>
            </span>
        </span>
        {{-- table section --}}
        <table class="rounded-lg border-2 border-[#f4f4f4] border-separate w-full min-h-[550px] max-h-[550px] block overflow-y-auto overflow-x-auto border-spacing-[0]">
            <thead class="rounded-lg bg-[#f2f2f2] border-2 border-[#f4f4f4] border-separate">
                <tr>
                    <th class="cursor-pointer hover:bg-[#e6e6e6] select-none">
                        <span class="flex justify-between">
                            #
                        </span>
                    </th>
                    @foreach ($headers as $header )
                        <th class="cursor-pointer hover:bg-[#e6e6e6] select-none">
                            <span class="flex justify-between">
                                {{ $header }}
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="InfoTable" class="bg-white rounded-lg">
                    {!! $DisplayTableInfo !!}
            </tbody>
        </table>
        {{-- bottom section --}}
        <div class="flex justify-between lg:mt-4 lg:flex-row md:flex-row flex-col">
            <label class="text-gray-600">Showing <span id="LogCount">0</span> Results</label>
        </div>
    </div>
    </div>
    <livewire:alert.notification key="{{ Str::random() }}"></livewire:alert.notification>
</div>
        @script
        <script>
            let AddMenuStatus = false;
            let EditMenuStatus = false;
            let DeleteMenuStatus = false;
            let headers = $wire.headers;
            let application = "";
            let ActionsDone = [];
            let TableObjects = [];
            let setStartDate = moment().subtract(6,"days");
            let setEndDate = moment().subtract(0,"day");
            let TimeFrame = "LAST 7 DAYS";
            let OGTable = [];
            let picker = new DateRangePicker("#DateRangePicker",{
                minDate:moment().subtract(12,"months"),
                maxDate:new Date(),
                endDate: moment().subtract(0,"day"),
                startDate: moment().subtract(6,"days"),
                ranges:{
                    "Today":[moment().subtract(0,"day"),moment().subtract(0,"day")],
                    "Yesterday": [moment().subtract(1,"day"),moment().subtract(1,"day")],
                    "Last 3 days": [moment().subtract(2,"days"),moment().subtract(0,"day")],
                    "Last 7 days": [moment().subtract(6,"days"),moment().subtract(0,"day")],
                    "Last 14 days": [moment().subtract(13,"days"),moment().subtract(0,"day")],
                    "Last 30 days": [moment().subtract(29,"days"),moment().subtract(0,"day")],
                    "Last 3 months": [moment().subtract(2,"months"),moment().subtract(0,"day")],
                    "Last 12 months": [moment().subtract(11,"months"),moment().subtract(0,"day")],
                },
            },async function(startDate,endDate, label){
                if (label.toLowerCase() == "custom range"){
                    let Offset = new Date().getTimezoneOffset();
                    let HourOffset = Offset/60;
                    //subtracting offset to have it convert correctly to unix time
                    let NewStartDate = new Date(startDate).setHours(0 + (-1 * HourOffset),0,0);
                    let NewEndDate = new Date(endDate).setHours(23 + (-1 * HourOffset),59,59)

                    setStartDate = JSON.stringify(new Date(NewStartDate));
                    setEndDate = JSON.stringify(new Date(NewEndDate));
                    TimeFrame = startDate.format('L') + "-" + endDate.format('L');
                    await SetTimeFrame();
                }
                else{
                     let Offset = new Date().getTimezoneOffset();
                    let HourOffset = Offset/60;
                    //subtracting offset to have it convert correctly to unix time
                    let NewStartDate = new Date(startDate).setHours(0 + (-1 * HourOffset),0,0);
                    let NewEndDate = new Date(endDate).setHours(23 + (-1 * HourOffset),59,59)

                    setStartDate = JSON.stringify(new Date(NewStartDate));
                    setEndDate = JSON.stringify(new Date(NewEndDate));
                    TimeFrame = label.toUpperCase();
                    await SetTimeFrame();
                }
            });
            async function SetTimeFrame(){
                await $wire.set("StartDate",setStartDate,false);
                await $wire.set("EndDate",setEndDate,false);
                await $wire.set("TimeFrame",TimeFrame,false);
                await refresh();
            }
            function CurrentDateAsString(){
                let Month = ((new Date().getMonth()+1).length == 2) ? (new Date().getMonth()+1) : "0" + (new Date().getMonth()+1)
                let Day =   (new Date().getDate().toString().length == 2) ? new Date().getDate() : "0" + (new Date().getDate())
                let Year = new Date().getFullYear()
                let date = Year + "-" + Month + "-" + Day;
                return date
            }
            function CurrentDateTimeAsString(){
                let Month = ((new Date().getMonth()+1).length == 2) ? (new Date().getMonth()+1) : "0" + (new Date().getMonth()+1)
                let Day =   (new Date().getDate().toString().length == 2) ? new Date().getDate() : "0" + (new Date().getDate())
                let Year = new Date().getFullYear()
                let Hours = (new Date().getHours().toString().length == 2) ? new Date().getHours() : "0"+ new Date().getHours();
                let Minutes = (new Date().getMinutes().toString().length == 2) ? new Date().getMinutes() : "0"+ new Date().getMinutes();
                let Seconds = (new Date().getSeconds().toString().length == 2) ? new Date().getSeconds() : "0"+ new Date().getSeconds();
                let Time = Hours + ":" + Minutes + ":" + Seconds;
                let date = Year + "-" + Month + "-" + Day;
                let DateTime = date + "T" + Time;
                return DateTime;
            }
            function PopulateArrayWithVals(Log){
                let FormVals = [];
                FormVals.push($(`#${Log} #startTime`).val());
                FormVals.push($(`#${Log} #endTime`).val());
                return FormVals;
            }
            $js("refresh",async function(){
                TimeFrame = "LAST 7 DAYS";
                picker.startDate = moment().subtract(6,"days");
                picker.endDate = moment().subtract(0,"day");
                
                let Offset = new Date().getTimezoneOffset();
                let HourOffset = Offset/60;
                //subtracting offset to have it convert correctly to unix time
                let NewStartDate = new Date(moment().subtract(6,"days")).setHours(0 + (-1 * HourOffset),0,0);
                let NewEndDate = new Date(moment().subtract(0,"day")).setHours(23 + (-1 * HourOffset),59,59)
                
                setStartDate = JSON.stringify(new Date(NewStartDate));
                setEndDate = JSON.stringify(new Date(NewEndDate));
                await $wire.set("StartTime", '00:00',false);
                await $wire.set("EndTime", '23:59',false);
                await $wire.call("LoadUsersOrganization");
                await SetTimeFrame();
            })
            function UpdateShowingCount(){
                $("#LogCount").text($("#InfoTable").children().length);
            }
            async function refresh(){
                //reset actions done
                ActionsDone = [];
                //now that everything is unchecked we re-load the table and org
                await $wire.call("LoadOrganizations");
                await $wire.call("LoadInfo");
                //re-gen sequence nums
                $("#InfoTable").children().each(function(index){
                    $(this).children()[0].textContent = index+1;
                })
                UpdateShowingCount();
                PrepFileForExport();
                OGTable = [];
                $("#InfoTable").children().each(function(index){
                    OGTable.push($(this).clone(true,true));
                });
                $("#SearchBarLogs").on('input',function(ev){
                    SearchThroughTable($("#SearchBarLogs").val());
                })
                $("table thead th").off("click").on("click", function() {
                    //header the table belongs to
                    var table = $(this).closest("table");
                    //getting the element from the table
                    var tbody = table.find("tbody");

                    //getting all the rows and putting them in an array
                    var rows = tbody.find("tr").toArray();
                    //index of the header clicked
                    var index = $(this).index();

                    //sort and toggle the order. checking to see if it has asc, if this does switch to desc
                    var asc = !$(this).hasClass("asc");

                    //need to make sure to remove the header class to put in a new one
                    table.find("th").removeClass("asc desc");
                    //depending on the 
                    $(this).toggleClass("asc", asc);
                    $(this).toggleClass("desc", !asc);

                    //this will sort the tables now
                    rows.sort((a, b) => {

                        var UP = $(a).children("td").eq(index).text().toUpperCase();
                        var DOWN = $(b).children("td").eq(index).text().toUpperCase();

                        if ($.isNumeric(UP) && $.isNumeric(DOWN)){
                            UP = Number(UP)
                            DOWN = Number(DOWN)
                        }
                        
                        if (UP < DOWN) {
                            return asc ? -1 : 1;
                        }
                        if (UP > DOWN) {
                            return asc ? 1 : -1;
                        }
                        return 0;
                        
                    });

                    //place the new rows back into the table (append them)
                    $.each(rows, (i, row) => {
                        tbody.append(row);
                    });
                 });
            }
            $("#TriggerOpenFilter").click(function(e){
                OpenCloseFilter()
            })
            $("#Filter").on("click",function(e){
                OpenCloseFilter()
            });
            $js("setDevice",async function(deviceEUI){
                try{
                    OpenCloseDevice()
                    await $wire.call("SetDevice",deviceEUI);
                    await refresh();
                }
                catch(e){

                }
            })
            function OpenCloseFilter(){
                if ($("#FilterDropDown").attr("isopen") == "false"){
                    $("#FilterDropDown").addClass("transition ease-out duration-100");
                    $("#FilterDropDown").removeClass("transform opacity-0 scale-0");
                    $("#FilterDropDown").addClass("transform opacity-100 scale-100");
                    $("#FilterDropDown").attr("isOpen",true);
                    $("#startTime").val("00:00");
                    $("#endTime").val("23:59");
                    }
                else{
                    $("#FilterDropDown").removeClass("transition ease-out duration-100");
                    $("#FilterDropDown").addClass("transition ease-in duration-75");
                    $("#FilterDropDown").addClass("transform opacity-0 scale-0");
                    $("#FilterDropDown").removeClass("transform opacity-100 scale-100");
                    $("#FilterDropDown").attr("isOpen",false);
                }
            }
            function OpenCloseDevice(){
                if ($("#DeviceDropDown").attr("isopen") == "false"){
                    $("#DeviceDropDown").addClass("transition ease-out duration-100");
                    $("#DeviceDropDown").removeClass("transform opacity-0 scale-0");
                    $("#DeviceDropDown").addClass("transform opacity-100 scale-100");
                    $("#DeviceDropDown").attr("isOpen",true);
                    }
                else{
                    $("#DeviceDropDown").removeClass("transition ease-out duration-100");
                    $("#DeviceDropDown").addClass("transition ease-in duration-75");
                    $("#DeviceDropDown").addClass("transform opacity-0 scale-0");
                    $("#DeviceDropDown").removeClass("transform opacity-100 scale-100");
                    $("#DeviceDropDown").attr("isOpen",false);
                }
            }
            
            $("#Devices").on("click",function(e){
                OpenCloseDevice();
            })
            function OpenCloseSensor(){
                if ($("#SensorDropDown").attr("isopen") == "false"){
                    $("#SensorDropDown").addClass("transition ease-out duration-100");
                    $("#SensorDropDown").removeClass("transform opacity-0 scale-0");
                    $("#SensorDropDown").addClass("transform opacity-100 scale-100");
                    $("#SensorDropDown").attr("isOpen",true);
                    }
                else{
                    $("#SensorDropDown").removeClass("transition ease-out duration-100");
                    $("#SensorDropDown").addClass("transition ease-in duration-75");
                    $("#SensorDropDown").addClass("transform opacity-0 scale-0");
                    $("#SensorDropDown").removeClass("transform opacity-100 scale-100");
                    $("#SensorDropDown").attr("isOpen",false);
                }
            }
            $("#Sensors").on("click",function(){
                OpenCloseSensor();
            })
            $js("setSensor",async function(sensor_id){
                try{
                    OpenCloseSensor();
                    await $wire.call("SetSensor",sensor_id);
                    await refresh();
                }
                catch(e){

                }
            })
            $js("Filter",async function(){
                let vals = PopulateArrayWithVals("FilterDropDown");
                if (vals[1] == "" || vals[2] == ""){
                    return;
                }
                await $wire.set("StartTime",vals[0],false);
                await $wire.set("EndTime",vals[1],false);
                await refresh();
            })
            function SearchThroughTable(searchInput){
                try{
                    let FilteredTable = []
                    let OGTableCopy = [];
                    $(OGTable).each(function(index){
                        OGTableCopy.push($(this).clone(true,true));
                    })
                    if (searchInput != ""){
                        $(OGTableCopy).each(function(index){
                            let AlreadyAdded = false;
                            $(this).children().each(function(index){
                                //adding tr to filtered list
                                if ($(this).text().toLowerCase().includes(searchInput.toLowerCase()) && AlreadyAdded == false){
                                    FilteredTable.push($(this).parent());
                                    AlreadyAdded = true;
                                }
                                //highlighting matching bits
                                if ($(this).text().toLowerCase().includes(searchInput.toLowerCase())){

                                    let CleanInput = CleanseSearch(searchInput.toLowerCase());
                                    let Regex = new RegExp(""+CleanInput+"","ig")
                                    let NewText = $(this).text();
                                    NewText = NewText.replace(Regex,'<span class="bg-yellow-500 text-white">$&</span>');
                                    $(this).html(NewText);
                                }
                            })
                        })
                    }
                    $("#InfoTable").empty();

                    if (FilteredTable.length == 0){
                        $(OGTable).each(function(index){
                            $("#InfoTable").append($(this));
                        })
                        UpdateShowingCount();
                    }
                    else{
                        $(FilteredTable).each(function(index){
                            $("#InfoTable").append($(this));
                        })
                        UpdateShowingCount();
                    }
                }
                catch(e){
                    console.log(e);
                }
            }
            function CleanseSearch(input){
                try{
                    //add more as needed...
                    input = input.replace("(","\\(");
                    input = input.replace(")","\\)");
                    input = input.replace("{","\\{");
                    input = input.replace("}","\\}");
                    console.log(input);
                    return input;
                    
                }   
                catch(e){
                    console.log(e);
                }
            }
            $js("ChangeOrg",async function(ev,Org){
                await $wire.call("SetOrg",Org)
                await $wire.call("LoadDevicesBasedOnOrg");
                await refresh();
            })
            //generate Sequence Numbers on load ------------------------------------------------------------------------ON LOAD SEGMENT---------------------------
            $(document).ready(async function(){
                await $wire.set("StartDate",JSON.stringify(setStartDate),false);
                await $wire.set("EndDate",JSON.stringify(setEndDate),false);
                await $wire.call("LoadUsersOrganization");
                $wire.call("LoadDevicesBasedOnOrg");
                await refresh();
            })
            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            function SpaceToUnderScore(input){
                return input.replaceAll(" ","_");
            }
            function TRToObject(tr){
                let Values = [];
                tr.children().each(function(){
                    Values.push($(this).text());
                })
                Values.splice(0,1);
                let Obj = {}
                for (let i = 0; i < headers.length; i++){
                    Obj[headers[i]] = Values[i];
                }
                return Obj;
            }
            function ObjectToTR(obj){
                let Tr = document.createElement("tr");
                $.each(obj, function(key,value){
                    let td = document.createElement("td");
                    td.textContent = value;
                    Tr.appendChild(td);
                });
                return Tr;
            }
            function PrepFileForExport(){
                TableObjects = [];
                $("#InfoTable").children().each(function(index){
                    let OBJ = TRToObject($(this))
                    TableObjects.push(OBJ);
                });
                
            }
            function exportToCsv(filename, rows) {
                try{
                    const processRow = function (obj) {
                        let finalVal = '';
                        $.each(obj,function(key,value){
                            if (value.includes(",")){
                                value = "\""+value+"\"";
                            }
                            finalVal+=value + ",";
                        })
                        finalVal = finalVal.substr(0,finalVal.length-1);
                        return finalVal + '\n';
                    };

                    //puts in headers
                    let csvFile = '';
                    $.each(headers,function(key,value){
                        csvFile+=value+","
                    })
                    csvFile = csvFile.substr(0,csvFile.length-1);
                    csvFile+='\n';

                    //puts in rows
                    for (let i = 0; i < rows.length; i++) {
                        csvFile += processRow(rows[i]);
                    }
                    //generates download
                    const blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement("a");
                    const url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    return true;
                }
                catch(ex){
                    return false;
                }
                
            }
            $js("DownloadCSV",async function(){
                if (TableObjects.length != 0){
                    let result = exportToCsv("SensorReadingInfo.csv",TableObjects);
                    await $wire.call("LogExport");
                    await refresh();
                    if (result == true){
                        setAlertText("Exported to CSV");
                        displayAlert();
                    }
                    else{
                        setAlertText("Failed to export to CSV");
                        displayAlert();
                    }
                }
                else{
                    setAlertText("Please wait a moment...");
                    displayAlert();
                }
            });
    </script>
    @endscript