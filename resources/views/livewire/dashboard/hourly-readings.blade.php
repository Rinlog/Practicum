<div>
    {{-- Search Options --}}
    <div class="fixed lg:w-[16%] lg:h-full lg:top-15 overflow-y-scroll lg:overflow-y-scroll bottom-5 h-[25%] right-0 bg-[#f9f9f9] lg:bg-white p-4 shadow-md z-2"]>
        {{-- DatePicker --}}
        <button id="DateRangePicker" class="flex justify-between bg-[#0071a0] mt-6 p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[240px]">
            <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" viewBox="0 0 26 26" class="size-5 min-h-[26px] min-w-[26px]">
                <path id="Calendar" class="cls-1" d="M20.5,3h-1.5v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-8v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-1.5c-1.93,0-3.5,1.57-3.5,3.5v15c0,1.93,1.57,3.5,3.5,3.5h15c1.93,0,3.5-1.57,3.5-3.5V6.5c0-1.93-1.57-3.5-3.5-3.5ZM5.5,5h1.5v2c0,.55.45,1,1,1s1-.45,1-1v-2h8v2c0,.55.45,1,1,1s1-.45,1-1v-2h1.5c.83,0,1.5.67,1.5,1.5v4H4v-4c0-.83.67-1.5,1.5-1.5ZM20.5,23H5.5c-.83,0-1.5-.67-1.5-1.5v-9h18v9c0,.83-.67,1.5-1.5,1.5Z"/>
            </svg>
            <label id="DateRangeText" class="cursor-pointer">{{ $TimeFrame }}</label>
            <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
            </svg>
        </button>
        {{-- TimePicker --}}
        <div id="FilterContainer" class="relative mt-6">                
                    <button id="Filter" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[240px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none">
                            <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.24 16.24L12 12" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <label id="TimeRange" class="cursor-pointer">Hour {{$StartTime}} - {{ $EndTime }}</label>
                        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div id="FilterDropDown" isOpen="false" class="absolute right-0 z-3 mt-2 w-90 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="p-4 flex flex-col items-center" role="none">
                            <livewire:components.req-underline-input id="startTime" text="Start Time*" textColor="text-gray-500" inputColor="text-gray-600" type="number"></livewire:components.req-underline-input>
                            <livewire:components.req-underline-input id="endTime" text="End Time*" textColor="text-gray-500" inputColor="text-gray-600" type="number"></livewire:components.req-underline-input>
                        </div>
                        <span class="flex flex-row justify-center items-center p-4">
                            <button wire:click="$js.Filter" class="bg-white border-2 border-[#46c0e5] p-3 rounded-full text-[#46c0e5] font-semibold hover:text-[#3c8fb0] hover:border-[#3c8fb0] cursor-pointer w-full">
                                CONFIRM
                            </button>
                        </span>
                    </div>
        </div>
        {{-- Application --}}
        <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
            <label class="pl-2 text-lg">Application:</label>
            <select id="applications" class="w-full pl-2" wire:change="$js.DisplayInfoBasedOnApp($event.target.value)">
                @foreach ($applications as $application)
                    <option value="{{ $application->application_id }}" id={{ $application->application_id }}>{{ $application->application_name }}</option>
                @endforeach
            </select>
        </div> 
        {{-- Location --}}
        <div id="LocationContainer" class="mt-6">
            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                <label class="pl-2 text-lg">Location:</label>
                <select id="locations" class="w-full pl-2" wire:change="$js.DisplaySubLocationBasedOnLocation">
                    @foreach ($locations as $location)
                        <option value="{{ $location->location_id }}" id={{ $location->location_id }}>{{ $location->location_name }}</option>
                    @endforeach
                </select>
            </div> 
            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                <label class="pl-2 text-lg">Sub-Location:</label>
                <select id="subLocations" class="w-full pl-2" wire:change="$js.DisplayDevicesBasedOnLocationSubLocationAndDeviceType" >
                    @foreach ($subLocations as $subLocation)
                        <option value="{{ $subLocation->sub_location_id }}" id={{ $subLocation->sub_location_id }}>{{ $subLocation->sub_location_name }}</option>
                    @endforeach
                </select>
            </div> 
        </div>
        {{-- Device --}}
        <div class="DeviceContainer"> 
            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                <label class="pl-2 text-lg">Device Types:</label>
                <select id="deviceTypes" class="w-full pl-2" wire:change="$js.DisplayDevicesBasedOnLocationSubLocationAndDeviceType">
                    @foreach ($deviceTypes as $type)
                        <option value="{{ $type->device_type_id }}" id={{ $type->device_type_id }}>{{ $type->device_type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                <label class="pl-2 text-lg">Devices:</label>
                <select id="devices" class="w-full pl-2" wire:change="$js.DisplaySensorsBasedOnDevice($event.target.value)">
                    @foreach ($devices as $device)
                        <option value="{{ $device->device_eui }}" id={{ $device->device_eui }}>{{ $device->device_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Sensor --}}
        <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
            <label class="pl-2 text-lg">Sensors:</label>
            <select id="sensors" class="w-full pl-2">
                 @foreach ($sensors as $sensor)
                    <option id={{ $sensor->sensor_id }}>{{ $sensor->sensor_name }}</option>
                @endforeach
            </select>
        </div>
        {{-- Data Items --}}
        @if ($displayDataItems == true)
            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                <label class="pl-2 text-lg">Data Items:</label>
                <select id="dataItems" class="w-full pl-2" wire:model="selectedDataItem">
                   @foreach ($dataItems as $dataItem)
                    <option>{{ $dataItem }}</option>
                   @endforeach
                </select>
            </div>
        @endif
        {{-- Confirm --}}
        <span class="flex flex-row justify-center items-center p-4 flex-1 sm:pt-20 sm:pb-20">
            <button wire:click="$js.Search" class="bg-white border-2 border-[#46c0e5] p-3 rounded-full text-[#46c0e5] font-semibold hover:text-[#3c8fb0] hover:border-[#3c8fb0] cursor-pointer w-full">
                CONFIRM
            </button>
        </span>
     </div>
     {{-- Graph Section --}}
     <div id="Graphs" class="sm:grid sm:grid-cols-3">
        
     </div>
     {{-- Table Section --}}
     <div>
        {{-- Export Button --}}
        <div class="p-2">
            <button id="Export" wire:click="$js.DownloadCSV" class="export flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px" class="svg" fill="#46c0e5">
                        <path id="Export" class="cls-1" d="M16.71,13.62c.39.39.39,1.02,0,1.41-.2.2-.45.29-.71.29s-.51-.1-.71-.29l-1.29-1.29v3.93c0,.55-.45,1-1,1s-1-.45-1-1v-3.93l-1.29,1.29c-.39.39-1.02.39-1.41,0s-.39-1.02,0-1.41l3-3c.38-.38,1.04-.38,1.41,0l3,3ZM23,8v12.81c-.03,2.31-1.94,4.19-4.29,4.19H7.31s-.05,0-.06,0c-2.31,0-4.22-1.88-4.25-4.22V5.19c.03-2.31,1.94-4.19,4.25-4.19h.03s8.71,0,8.71,0c.53,0,1.04.21,1.41.59l5,5c.38.38.59.88.59,1.41ZM17,7h3l-3-3v3ZM21,9h-4c-1.1,0-2-.9-2-2v-4h-7.71s-.02,0-.03,0c-1.23,0-2.24.99-2.25,2.22v15.56c.02,1.23,1.02,2.22,2.25,2.22.01,0,.02,0,.03,0h11.43s.02,0,.03,0c1.23,0,2.24-.99,2.25-2.22v-11.78Z"/>
                    </svg>
                    EXPORT
            </button>
        </div>
        <table class="rounded-lg border-2 border-[#f4f4f4] border-separate w-full min-h-[550px] max-h-[550px] block overflow-y-auto overflow-x-auto border-spacing-[0]">
            <thead id="InfoHeader" class="rounded-lg bg-[#f2f2f2] border-2 border-[#f4f4f4] border-separate">
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
                        @foreach($groupedJsonReadings as $Row)
                            <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $Row[0][0] }}</td>
                            <td>{{ $Row[0][1] }}</td>
                            @foreach($Row[1] as $Readings)
                                @foreach ($Readings as $key=>$Reading)
                                    @if (is_object($Reading))
                                        @if (is_array((array)$Reading))
                                            @if ($displayDataItems == false)
                                                @foreach ($Reading as $DeepKey=>$DeepReading)
                                                    @if (strtolower($DeepKey) == "total")
                                                        <td>
                                                            {{ round($DeepReading/$Row[0][2],2) }}
                                                        </td>
                                                    @elseif (str_contains(strtolower($DeepKey),"minimum") || str_contains(strtolower($DeepKey),"maximum"))
                                                        @foreach ($DeepReading as $MinMaxKey=>$MinMaxVal)
                                                        <td>
                                                            {{ $MinMaxVal }}
                                                        </td>
                                                        @endforeach
                                                    @else
                                                        <td>
                                                            {{ $DeepReading }}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            @elseif ($displayDataItems == true)
                                                @if ($selectedDataItem == $key)
                                                    @foreach ($Reading as $DeepKey=>$DeepReading)
                                                        @if (str_contains(strtolower($DeepKey),"minimum") || str_contains(strtolower($DeepKey),"maximum"))
                                                            @foreach ($DeepReading as $MinMaxKey=>$MinMaxVal)
                                                            <td>
                                                                {{ $MinMaxVal }}
                                                            </td>
                                                            @endforeach
                                                        @else
                                                            <td>
                                                                {{ $DeepReading }}
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        @else
                                            <td>
                                                {{ $Reading }}
                                            </td>
                                        @endif
                                    @else
                                        <td>
                                            {{ $Reading }}
                                        </td>
                                    @endif
                                @endforeach
                            @endforeach
                            </tr>
                        @endforeach
                    <td id="LoadingIcon" class="relative align-center h-[490px] hide" colspan="999" wire:loading.class.remove="hide">
                        <span class="absolute top-[35%] left-[45%]" wire:loading>
                            <img src="/images/Loading_2.gif">
                        </span>
                    </td>
            </tbody>
        </table>
     </div>
     <livewire:alert.notification></livewire:alert.notification>
</div>
        @script
        <script>
            let headers;
            let application = "";
            let ActionsDone = [];
            let TableObjects = [];
            let Offset = new Date().getTimezoneOffset();
            let HourOffset = Offset/60;
            let setStartDate = JSON.stringify(new Date(new Date(moment().subtract(0,"day")).setHours(0 + (-1 * HourOffset),0,0)));
            let setEndDate = JSON.stringify(new Date(new Date(moment().subtract(0,"day")).setHours(23 + (-1 * HourOffset),59,59)));
            let TimeFrame = "TODAY";
            let OGTable = [];
            
            let applications;
            let locations;
            let sublocations;
            let appLocationsAssoc;
            let appDeviceTypeAssoc;
            let deviceDeploymentInfo;
            let devices;
            let deviceTypes;
            let deviceSensorAssoc;
            let sensors;
            let groupedJsonReadings;
            let normalDisplay;
            let selectedDataItem;
            let displayDataItem;
            let picker = new DateRangePicker("#DateRangePicker",{
                minDate:moment().subtract(12,"months"),
                maxDate:new Date(),
                endDate: moment().subtract(0,"day"),
                startDate: moment().subtract(0,"days"),
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
                    //subtracting offset to have it convert correctly to unix time
                    let NewStartDate = new Date(startDate).setHours(0 + (-1 * HourOffset),0,0);
                    let NewEndDate = new Date(endDate).setHours(23 + (-1 * HourOffset),59,59)

                    setStartDate = JSON.stringify(new Date(NewStartDate));
                    setEndDate = JSON.stringify(new Date(NewEndDate));
                    TimeFrame = startDate.format('L') + "-" + endDate.format('L');
                    await SetTimeFrame();
                }
                else{
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
                $("#DateRangeText").text(TimeFrame);
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
            //this refresh function is not being used, it's just here in case it is needed at some point
            $js("refresh",async function(){
                ShowLoading();
                TimeFrame = "TODAY";
                picker.startDate = moment().subtract(0,"days");
                picker.endDate = moment().subtract(0,"day");
                
                let Offset = new Date().getTimezoneOffset();
                let HourOffset = Offset/60;
                //subtracting offset to have it convert correctly to unix time
                let NewStartDate = new Date(moment().subtract(0,"days")).setHours(0 + (-1 * HourOffset),0,0);
                let NewEndDate = new Date(moment().subtract(0,"day")).setHours(23 + (-1 * HourOffset),59,59)
                
                setStartDate = JSON.stringify(new Date(NewStartDate));
                setEndDate = JSON.stringify(new Date(NewEndDate));
                await $wire.set("StartTime", '0',false);
                await $wire.set("EndTime", '23',false);
                await SetTimeFrame();
            });
            function UpdateShowingCount(){
                $("#LogCount").text($("#InfoTable").children().length-1);
            }
            async function refresh(LogExport = false){
                //reset actions done
                ActionsDone = [];
                //now that everything is we re-load the table
                let App = $("#applications").children(":selected").attr("id");
                let Location = $("#locations").children(":selected").attr("id");
                let SubLocation = $("#subLocations").children(":selected").attr("id");
                let Device = $("#devices").children(":selected").attr("id");
                let Sensor = $("#sensors").children(":selected").attr("id");
                if (LogExport == true){
                    ShowLoading();
                    await $wire.call("LogExport");
                }
                ShowLoading();
                await $wire.call("LoadInfo",[Device,Sensor]);
                headers = $wire.headers;
                normalDisplay = $wire.normalDisplay;
                groupedJsonReadings = $wire.groupedJsonReadings;
                displayDataItem = $wire.displayDataItems;
                selectedDataItem = $wire.selectedDataItem;
                UpdateShowingCount();
                PrepFileForExport();
                OGTable = [];
                $("#InfoTable").children().each(function(index){
                    OGTable.push($(this).clone(true,true));
                });
                $("#SearchBarLogs").on('input',function(ev){
                    SearchThroughTable($("#SearchBarLogs").val());
                })
                await DisplayInfoBasedOnApp(applications[0]["application_id"]);
                //re-choosing previous selections
                $("applications"+App).prop("selected",true);
                DisplayInfoBasedOnApp(App);
                $("#locations #"+Location).prop("selected",true);
                DisplaySubLocationBasedOnLocation();
                $("#subLocations #"+SubLocation).prop("selected",true);
                DisplayDevicesBasedOnLocationSubLocationAndDeviceType();
                $("#devices #"+Device).prop("selected",true);
                DisplaySensorsBasedOnDevice(Device);
                $("#sensors #"+Sensor).prop("selected",true);

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
                 GenChart();
            }
            function PopulateArrayWithVals(Input){
                let FormVals = [];
                FormVals.push($(`#${Input} #startTime`).val());
                FormVals.push($(`#${Input} #endTime`).val());
                return FormVals;
            }
            $js("Filter",async function(){
                OpenCloseFilter();
                let vals = PopulateArrayWithVals("FilterDropDown");
                console.log(vals[0]);
                if (vals[0] == "" || vals[1] == ""){
                    setAlertText("Invalid Hours");
                    displayAlert();
                    return;
                }
                else if (vals[0] < 0 || vals[0] > 23){
                    setAlertText("Start hour invalid");
                    displayAlert();
                    return;
                }
                else if (vals[1] > 23 || vals[1] < 0){
                    setAlertText("End hour invalid");
                    displayAlert();
                    return;
                }
                else if (vals[0] > vals[1]){
                    setAlertText("Start hour must be before end hour");
                    displayAlert();
                    return;
                }
                await $wire.set("StartTime",vals[0],false);
                await $wire.set("EndTime",vals[1],false);
                $("#TimeRange").text("Hour " + vals[0] + " - " + vals[1]);

            })
            $js("Search",async function(){
                $("#Graphs").text("");
                ShowLoading();
                await refresh();
            });
            function ShowLoading(){
                $("#InfoHeader").html("<tr><th>Loading...</th></tr>");
                let LoadingTD = $("#InfoTable #LoadingIcon");
                LoadingTD.html(
                    "<span class=\"absolute top-[35%] left-[45%]\" wire:loading colspan=\"999\"><img src=\"/images/Loading_2.gif\"></span>"
                )
                $("#InfoTable").text(""); //clearing current Info
                $("#InfoTable").append(LoadingTD);
            }
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
                    return input;
                    
                }   
                catch(e){
                    console.log(e);
                }
            }
            //generate Sequence Numbers on load ------------------------------------------------------------------------ON LOAD SEGMENT---------------------------
            $(document).ready(async function(){
                await $wire.set("StartDate",JSON.stringify(setStartDate),false);
                await $wire.set("EndDate",JSON.stringify(setEndDate),false);
                await $wire.call("LoadOptions");
                applications = $wire.applications
                locations = $wire.locations;
                sublocations = $wire.subLocations;
                appLocationsAssoc = $wire.applicationLocationAssoc;
                deviceDeploymentInfo = $wire.deviceDeploymentInfo;
                devices = $wire.devices;
                deviceTypes = $wire.deviceTypes;
                deviceSensorAssoc = $wire.deviceSensorAssoc;
                sensors = $wire.sensors;
                appDeviceTypeAssoc = $wire.applicationDeviceTypeAssoc
                await DisplayInfoBasedOnApp(applications[0]["application_id"]);
                await refresh();
            })
            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            function SpaceToUnderScore(input){
                return input.replaceAll(" ","_");
            }
            $("#Filter").on("click",function(e){
                OpenCloseFilter()
            });
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
            function CleanseTD(text){
                try{
                    text = text.replace("\n","");
                    text = text.trim();
                    return text;
                }
                catch(e){
                    console.log(e);
                }
            }
            function TRToObject(tr){
                let Values = [];
                tr.children().each(function(){
                    Values.push(CleanseTD($(this).text()));
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
                TableObjects.pop();
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
                    let result = exportToCsv("HourlySensorReadingInfo.csv",TableObjects);

                    await refresh(true);
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
            //--------------------------------------FRONTEND OPTION FILTERING-------------------------------------------------
            $js("DisplaySubLocationBasedOnLocation",DisplaySubLocationBasedOnLocation);
            $js("DisplayDevicesBasedOnLocationSubLocationAndDeviceType",DisplayDevicesBasedOnLocationSubLocationAndDeviceType);
            $js("DisplayInfoBasedOnApp",DisplayInfoBasedOnApp);
            $js("DisplaySensorsBasedOnDevice",DisplaySensorsBasedOnDevice);
            function DisplayInfoBasedOnApp(App){
                try{
                    let DisplayLocationIds = [];
                    //loading locations
                    $(appLocationsAssoc).each(function(index){
                        if (App == $(this)[0]["application_id"]){
                            DisplayLocationIds.push($(this)[0]["location_id"]);
                            
                        }
                    })
                    //filtering locations
                    let LocationsDetailed = locations.filter(item => {
                        if (item.location_name == "All Locations"){
                            return true;
                        }
                        else{
                            return DisplayLocationIds.includes(item.location_id)
                        }
                    });
                    //loading device types
                    let DisplayDeviceTypeIds = [];
                    $(appDeviceTypeAssoc).each(function(index){
                        if (App == $(this)[0]["application_id"]){
                            DisplayDeviceTypeIds.push($(this)[0]["device_type_id"]);
                        }
                    })
                    let DeviceTypesDetailed= deviceTypes.filter(item =>{
                        if (item.device_type == "All Device Types"){
                            return true;
                        }
                        else{
                            return DisplayDeviceTypeIds.includes(item.device_type_id);
                        }
                    });
                    //update locations
                    $("#locations").html("");
                    let InfoString = "";
                    $(LocationsDetailed).each(function(index){
                        InfoString+="<option value="+$(this)[0]["location_id"]+" id="+$(this)[0]["location_id"]+">"+$(this)[0]["location_name"]+"</option>"
                    })
                    $("#locations").html(InfoString);

                    //update device types
                    $("#deviceTypes").html("");
                    let InfoString2 = "";
                    $(DeviceTypesDetailed).each(function(index){
                        InfoString2+="<option value="+$(this)[0]["device_type_id"]+" id="+$(this)[0]["device_type_id"]+">"+$(this)[0]["device_type"]+"</option>"
                    })
                    $("#deviceTypes").html(InfoString2);

                    DisplaySubLocationBasedOnLocation();
                }
                catch(e){
                    console.log(e);
                }
            }
            function DisplaySubLocationBasedOnLocation(){
                try{
                    let Location = $("#locations").children(":selected").attr("id");
                    let LocationIds = $("#locations").children().toArray().map(function(elem,index){
                    if ($(elem).attr("id") == "all"){
                        return;
                    }
                    else{
                        return $(elem).attr("id");
                    }
                    });
                    let FilteredLocationIds = LocationIds.filter(item => item!==undefined);

                    let SubLocationsDetailed = sublocations.filter(item => {
                        if (item.sub_location_name == "All Sub Locations"){
                            return true;
                        }
                        if (Location == "all"){
                            return FilteredLocationIds.includes(item.location_id)
                        }
                        else{
                            return Location.includes(item.location_id)
                        }
                    });
                    $("#subLocations").html("");
                    let InfoString = "";
                    $(SubLocationsDetailed).each(function(index){
                        InfoString+="<option value="+$(this)[0]["sub_location_id"]+" id="+$(this)[0]["sub_location_id"]+">"+$(this)[0]["sub_location_name"]+"</option>"
                    })
                    $("#subLocations").html(InfoString);
                    DisplayDevicesBasedOnLocationSubLocationAndDeviceType();
                }
                catch(e){
                    console.log(e);
                }
            }
            function DisplayDevicesBasedOnLocationSubLocationAndDeviceType(){
                try{
                    let Location = $("#locations").children(":selected").attr("id");
                    let DeviceType = $("#deviceTypes").children(":selected").attr("id");
                    let SubLocation = $("#subLocations").children(":selected").attr("id");
                    //full device type list
                    let DeviceTypeIds = $("#deviceTypes").children().toArray().map(function(elem,index){
                    if ($(elem).attr("id") == "all"){
                        return;
                    }
                    else{
                        return $(elem).attr("id");
                    }
                    });
                    let FilteredDeviceTypeIds = DeviceTypeIds.filter(item => item!==undefined);
                    //full sublocation list
                    let SubLocationIds = $("#subLocations").children().toArray().map(function(elem,index){
                    if ($(elem).attr("id") == "all"){
                        return;
                    }
                    else{
                        return $(elem).attr("id");
                    }
                    });
                    let FilteredSubLocationIds = SubLocationIds.filter(item => item!==undefined);
                    //full location list
                    let LocationIds = $("#locations").children().toArray().map(function(elem,index){
                    if ($(elem).attr("id") == "all"){
                        return;
                    }
                    else{
                        return $(elem).attr("id");
                    }
                    });
                    let FilteredLocationIds = LocationIds.filter(item => item!==undefined);
                    //grab devices based on deviceType
                    let DevicesBasedOnDeviceType = devices.filter(item => {
                        if (DeviceType == "all"){
                            //if set to all we will check all device Types
                            return FilteredDeviceTypeIds.includes(item.device_type_id);
                        }
                        else{
                            return DeviceType.includes(item.device_type_id)
                        }
                    });
                    //grab the deployment for each device
                    let DeviceAssoc = deviceDeploymentInfo.filter(item =>{
                        let FilteredList = DevicesBasedOnDeviceType.filter(item2 => {
                            return item2.device_eui.includes(item.device_eui)
                        });
                        if (FilteredList.length > 0){
                            return true;
                        }
                        else{
                            return false;
                        }
                    });
                    //filter deployed devices by sublocation
                    let DevicesSubLocationFiltered = DeviceAssoc.filter(item => {
                        if (SubLocation == "all"){
                            return FilteredSubLocationIds.includes(item.sub_location_id);
                        }
                        else{
                            return SubLocation.includes(item.sub_location_id);
                        }
                    });
                    //filter deployed devices by location
                    let DevicesLocationFiltered = DevicesSubLocationFiltered.filter(item => {
                        if (Location == "all"){
                            return FilteredLocationIds.includes(item.location_id);
                        }
                        else{
                            return Location.includes(item.location_id);
                        }
                    });
                    //regrab device info with filtered info
                    let DevicesDetailed = devices.filter(item =>{
                        let FilteredList = DevicesLocationFiltered.filter(item2 =>{
                            return item2.device_eui.includes(item.device_eui);
                        })
                        if (FilteredList.length > 0){
                            return true;
                        }
                        else{
                            return false;
                        }
                    })
                    $("#devices").html("");
                    let InfoString = "";
                    $(DevicesDetailed).each(function(index){
                        InfoString+="<option value="+$(this)[0]["device_eui"]+" id="+$(this)[0]["device_eui"]+">"+$(this)[0]["device_name"]+"</option>"
                    })
                    $("#devices").html(InfoString);
                    if (DevicesDetailed.length > 0){
                        DisplaySensorsBasedOnDevice(DevicesDetailed[0]["device_eui"]);
                    }
                    else{
                        DisplaySensorsBasedOnDevice("none");
                    }
                }
                catch(e){
                    console.log(e);
                }
            }
            function DisplaySensorsBasedOnDevice(Device){
                try{
                    //sensor assoc holds all sensor info associated to device
                    let SensorsAssoc = deviceSensorAssoc.filter(item => {
                            return Device.includes(item.device_eui);
                    });
                    let SensorDetailed = sensors.filter(item =>{
                        if (item.sensor_id == "all"){
                            return true
                        }
                        let Sensors = SensorsAssoc.filter(item2 =>{
                            return item2.sensor_id.includes(item.sensor_id);
                        })
                        if (Sensors.length > 0){
                            return true;
                        }
                        else{
                            return false;
                        }
                    })
                    $("#sensors").html("");
                    let InfoString = "";
                    $(SensorDetailed).each(function(index){
                        InfoString+="<option value="+$(this)[0]["sensor_id"]+" id="+$(this)[0]["sensor_id"]+" >"+$(this)[0]["sensor_name"]+"</option>"
                    })
                    $("#sensors").html(InfoString);
                }
                catch(e){
                    console.log(e);
                }
            }
            //Chart JS--------------------------------------------------------------------------------------
            function GenChart(){
                Charts = {};
                //generating raw data for charts
                $.each(groupedJsonReadings,function(key,value){
                    $.each(value[1],function(k,v){
                        $.each(v,function(ReadingName,ReadingValue){
                            if (typeof ReadingValue == 'object' && normalDisplay == true){
                                $.each(ReadingValue,function(DeepReadingKey,DeepReadingVal){
                                    //first we are checking for the average based on the total field
                                    if (Charts[ReadingName + " Average"] !== undefined && DeepReadingKey.toLowerCase() == "total"){
                                        Charts[ReadingName + " Average"].push(Math.round(DeepReadingVal/value[0][2] * 100)/100);
                                    }
                                    else if (Charts[ReadingName + " Average"] === undefined){
                                        if (!(isNaN(DeepReadingVal))){
                                            Charts[ReadingName + " Average"] = [Math.round(DeepReadingVal/value[0][2] * 100)/100];
                                        }
                                    }
                                    //next we check all the maximum and minimum vals and add them to charts
                                    if (DeepReadingKey.toLowerCase() !== "total"){
                                        $.each(DeepReadingVal,function(MinMaxKey,MinMaxVal){
                                            if (Charts[ReadingName + " Maximum value"] !== undefined && !(isNaN(MinMaxVal)) && DeepReadingKey.toLowerCase() !== "minimum"){
                                                Charts[ReadingName + " Maximum value"].push(MinMaxVal);
                                            }   
                                            else if (Charts[ReadingName + " Minimum value"] !== undefined && !(isNaN(MinMaxVal)) && DeepReadingKey.toLowerCase() !== "maximum"){
                                                Charts[ReadingName + " Minimum value"].push(MinMaxVal);
                                            }
                                            else{
                                                if (!(isNaN(MinMaxVal))){
                                                    if (DeepReadingKey.toLowerCase() == "minimum"){
                                                        Charts[ReadingName + " Minimum value"] = [MinMaxVal];
                                                    }
                                                    else if (DeepReadingKey.toLowerCase() == "maximum"){
                                                        Charts[ReadingName + " Maximum value"] = [MinMaxVal];
                                                    }
                                                }
                                            }
                                        });
                                    }
                                })
                            }
                            else if (normalDisplay == false){
                                if (displayDataItem == false){
                                    if (typeof ReadingValue == "object"){
                                        $.each(ReadingValue,function(InnerReadingName,InnerReadingValue){
                                            if (ReadingName.toLowerCase().includes("maximum") || ReadingName.toLowerCase().includes("minimum")){
                                                if (Charts[ReadingName + " value"] !== undefined){
                                                    if (InnerReadingName == "value"){
                                                        Charts[ReadingName + " value"].push(InnerReadingValue);
                                                    }
                                                }
                                                else{
                                                    if (InnerReadingName == "value"){
                                                        Charts[ReadingName + " value"] = [InnerReadingValue]
                                                    }
                                                }
                                            }
                                        })
                                    }
                                    else{
                                        if (Charts[ReadingName] !== undefined){
                                            Charts[ReadingName].push(ReadingValue);
                                        }
                                        else{
                                            if (!(isNaN(ReadingValue))){
                                                Charts[ReadingName] = [ReadingValue];
                                            }
                                        }
                                    }
                                }
                                else if (displayDataItem == true){
                                    if (typeof ReadingValue == "object" && ReadingName == selectedDataItem){
                                        $.each(ReadingValue,function(InnerReadingName,InnerReadingValue){
                                            if (InnerReadingName.toLowerCase().includes("maximum") || InnerReadingName.toLowerCase().includes("minimum")){
                                                if (Charts[InnerReadingName + " value"] !== undefined){
                                                    Charts[InnerReadingName + " value"].push(InnerReadingValue["value"]);
                                                }
                                                else{
                                                    Charts[InnerReadingName + " value"] = [InnerReadingValue["value"]]
                                                }
                                            }
                                            else{
                                                if (Charts[InnerReadingName] !== undefined){
                                                    Charts[InnerReadingName].push(InnerReadingValue);
                                                }
                                                else{
                                                    if (!(isNaN(InnerReadingValue))){
                                                        Charts[InnerReadingName] = [InnerReadingValue];
                                                    }
                                                }
                                            }
                                        })
                                    }
                                }
                                
                            }
                        })
                    });
                })
                //generating html for charts
                $.each(Charts,function(k,v){
                    $("#Graphs").append(`<div class='min-h-[200px]'><canvas id='${k}'></canvas></div>`);
                    const ctx = document.getElementById(k);
                    let DispayChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                        labels: getSequence(Charts[k]),
                        datasets: [{
                            label: k,
                            data: Charts[k],
                            borderWidth: 1
                        }]
                        },
                        options: {
                        scales: {
                            y: {
                            beginAtZero: false
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        }
                    });
                })
            }
            function getSequence(Array){
                let Sequence = [];
                $.each(Array,function(index){
                    Sequence.push(index+1);
                });
                return Sequence;
            }
    </script>
    @endscript