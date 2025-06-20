<div id="MainWindowLogs" class="flex flex-col lg:flex-row lg:w-[1750px] gap-0">
    @vite(['resources/js/ComponentJS/FilterJS.js'])
    <div class="relative w-[90%] md:w-[80%] lg:w-[100%] pl-10 pr-10">
    <div class="lg:p-10 md:p-10 pb-15 pr-10 pl-2 pt-2 bg-white shadow-md flex flex-col gap-2 rounded-b-lg h-[1340px] w-full md:h-[781px] lg:h-[750px] overflow-y-hidden">
        <div id="ApplicationSelector" class="w-full flex items-center">
                <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md">Application</label>
                <div class="selectWrapperLG w-full">
                    <select id="Applications" class="open-sans-soft-regular border-r-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] w-[75%] text-white text-lg hover:bg-[#4a4a4a] p-6 pr-10 rounded-r-lg font-bold shadow-md">
                        @foreach($Applications as $application)
                            @if (isset($ApplicationInfo))
                                @if ($application->application_id == $ApplicationInfo->application_id)
                                    <option selected wire:click="$js.ChangeApplication($event,'{{ $application->application_id }}')" id="{{ $application->application_id }}">{{ $application->application_name }}</option>
                                @else
                                    <option wire:click="$js.ChangeApplication($event,'{{ $application->application_id }}')" id="{{ $application->application_id }}">{{ $application->application_name }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>
        </div>
        <span class="flex lg:flex-row lg:items-center md:flex-col flex-col lg:justify-between md:justify-between mb-4 gap-4">
            {{-- top half --}}
            {{-- calendar + filter --}}
            <span class="flex lg:flex-row lg:items-center md:flex-row md:items-center flex-col gap-4 items-start lg:flex-grow md:flex-grow">
                <button id="DateRangePicker" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[290px]">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" viewBox="0 0 26 26" class="size-5 min-h-[26px] min-w-[26px]">
                        <path id="Calendar" class="cls-1" d="M20.5,3h-1.5v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-8v-1c0-.55-.45-1-1-1s-1,.45-1,1v1h-1.5c-1.93,0-3.5,1.57-3.5,3.5v15c0,1.93,1.57,3.5,3.5,3.5h15c1.93,0,3.5-1.57,3.5-3.5V6.5c0-1.93-1.57-3.5-3.5-3.5ZM5.5,5h1.5v2c0,.55.45,1,1,1s1-.45,1-1v-2h8v2c0,.55.45,1,1,1s1-.45,1-1v-2h1.5c.83,0,1.5.67,1.5,1.5v4H4v-4c0-.83.67-1.5,1.5-1.5ZM20.5,23H5.5c-.83,0-1.5-.67-1.5-1.5v-9h18v9c0,.83-.67,1.5-1.5,1.5Z"/>
                    </svg>
                    <label id="DateRangeText" class="cursor-pointer">LAST 7 DAYS</label>
                    <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <span id="FilterContainer" class="relative">                
                    <button id="Filter" class="flex justify-between bg-[#0071a0] p-4 pr-6 pl-6 rounded-lg flex items-center gap-2 text-white font-semibold hover:bg-[#0486bd] cursor-pointer min-w-[290px]">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" class="min-h-[26px] size-5" viewBox="0 0 26 26">
                            <path id="Filter" class="cls-1" d="M22,3l-6.72,9.56v8.19l-4.51,2.26v-10.44L4,3h18M22,1H4c-.75,0-1.43.42-1.78,1.08-.34.66-.29,1.46.14,2.07l6.4,9.04v9.81c0,.69.36,1.34.95,1.7.32.2.69.3,1.05.3.31,0,.61-.07.89-.21l4.51-2.26c.68-.34,1.11-1.03,1.11-1.79v-7.55l6.35-9.04c.43-.61.48-1.41.14-2.07-.34-.66-1.03-1.08-1.77-1.08h0ZM22,5h0,0Z"/>
                        </svg>
                        <label class="cursor-pointer">FILTER</label>
                        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div id="FilterDropDown" isOpen="false" class="absolute left-0 z-10 mt-2 w-90 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="p-4 flex flex-col items-center" role="none">
                            <livewire:components.underline-input id="activityType" text="Activity Type" textColor="text-gray-500" inputColor="text-gray-600"></livewire:components.underline-input>
                            <livewire:components.req-underline-input id="startTime" text="Start Time*" textColor="text-gray-500" inputColor="text-gray-600" type="time"></livewire:components.req-underline-input>
                            <livewire:components.req-underline-input id="endTime" text="End Time*" textColor="text-gray-500" inputColor="text-gray-600" type="time"></livewire:components.req-underline-input>
                            <div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
                                <label class="pl-2 text-lg text-gray-500">User Name</label>
                                <select onchange="" id="userName" class="w-full pl-2">
                                    @foreach ($Users as $option)
                                        <option class="" id="{{ $option->user_id  }}" >{{ $option->user_username }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <span class="flex flex-row justify-center items-center p-4">
                            <button wire:click="$js.Filter" class="bg-white border-2 border-[#46c0e5] p-3 rounded-full text-[#46c0e5] font-semibold hover:text-[#3c8fb0] hover:border-[#3c8fb0] cursor-pointer w-full">
                                CONFIRM
                            </button>
                        </span>
                    </div>
                </span>
                {{-- Search bar --}}
                <div class="flex lg:w-full md:w-full relative">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 30 30" stroke="#666666" fill="#666666" class="mt-4 absolute left-3">
                        <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                    </svg>
                    <input type="text" id="SearchBarLogs" class="bg-white p-4 rounded-lg w-[80%] border-2 border-gray-200 pl-10" placeholder="Search by Keyword">
                </div>
            </span>
            {{-- refresh button --}}
            {{-- export button --}}
            <span class="flex gap-4 items-center">
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
        </span>
        {{-- table section --}}
        <table class="rounded-lg border-2 border-[#f4f4f4] border-separate w-full block overflow-y-auto overflow-x-auto border-spacing-[0]">
            <thead class="rounded-lg bg-[#f2f2f2] border-2 border-[#f4f4f4] border-separate">
                <tr>
                    <th>#</th>
                    @foreach ($headers as $header )
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="InfoTable" class="bg-white rounded-lg">
                    {!! $DisplayTableInfo !!}
            </tbody>
        </table>
        {{-- bottom section --}}
        <div class="flex justify-between lg:mt-4">
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
                    await SetTimeFrame();
                    TimeFrame = startDate.format('L') + "-" + endDate.format('L');
                    $("#DateRangeText").text(startDate.format('L') + "-" + endDate.format('L'))
                }
                else{
                     let Offset = new Date().getTimezoneOffset();
                    let HourOffset = Offset/60;
                    //subtracting offset to have it convert correctly to unix time
                    let NewStartDate = new Date(startDate).setHours(0 + (-1 * HourOffset),0,0);
                    let NewEndDate = new Date(endDate).setHours(23 + (-1 * HourOffset),59,59)

                    setStartDate = JSON.stringify(new Date(NewStartDate));
                    setEndDate = JSON.stringify(new Date(NewEndDate));
                    await SetTimeFrame();
                    TimeFrame = label.toUpperCase();
                    $("#DateRangeText").text(label.toUpperCase());
                }
            });
            async function SetTimeFrame(){
                await $wire.set("StartDate",setStartDate);
                await $wire.set("EndDate",setEndDate);
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
                FormVals.push($(`#${Log} #activityType`).val());
                FormVals.push($(`#${Log} #startTime`).val());
                FormVals.push($(`#${Log} #endTime`).val());
                FormVals.push($(`#${Log} #userName`).val());
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
                await $wire.set("StartDate",setStartDate);
                await $wire.set("EndDate",setEndDate);
                await $wire.set("ActivityType", "%");
                await $wire.set("StartTime", '00:00');
                await $wire.set("EndTime", '23:59');
                await $wire.set("User", "%");
                await refresh();
            })
            function UpdateShowingCount(){
                $("#LogCount").text($("#InfoTable").children().length);
            }
            async function refresh(){
                //reset actions done
                ActionsDone = [];
                //now that everything is unchecked we re-load the table and org
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
            }
            $("#Filter").click(function(e){
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
            });
            $js("Filter",async function(){
                let vals = PopulateArrayWithVals("FilterDropDown");
                if (vals[1] == "" || vals[2] == ""){
                    return;
                }
                await $wire.set("ActivityType",vals[0]);
                await $wire.set("StartTime",vals[1]);
                await $wire.set("EndTime",vals[2]);
                await $wire.set("User",vals[3]);
                await refresh();
                $("#DateRangeText").text(TimeFrame);
            })
            $js("ChangeApplication",async function(ev,Application){
                await $wire.call("SetApplication",Application)
                application = $wire.application;
                await refresh();
                $("#DateRangeText").text(TimeFrame);
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
                                if ($(this).text().toLowerCase().includes(searchInput) && AlreadyAdded == false){
                                    FilteredTable.push($(this).parent());
                                    AlreadyAdded = true;
                                }
                                //highlighting matching bits
                                if ($(this).text().toLowerCase().includes(searchInput)){

                                    let CleanInput = CleanseSearch(searchInput);
                                    let Regex = new RegExp(""+CleanInput+"","ig")
                                    let NewText = $(this).text();
                                    console.log(Regex);
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
            //generate Sequence Numbers on load ------------------------------------------------------------------------ON LOAD SEGMENT---------------------------
            $(document).ready(async function(){
                await $wire.set("StartDate",JSON.stringify(setStartDate));
                await $wire.set("EndDate",JSON.stringify(setEndDate));
                await $wire.call("LoadApplications");
                await $wire.call("SetDefaultApplication");
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
                    let result = exportToCsv("ApplicationLogInfo.csv",TableObjects);
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