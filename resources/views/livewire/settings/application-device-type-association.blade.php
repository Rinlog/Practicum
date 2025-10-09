<div id="MainWindowSettings" class="flex flex-col lg:flex-row lg:w-[1750px] gap-10">
    {{-- LEFT SIDE --}}
    <div class="relative w-[90%] md:w-[80%] lg:w-[70%]">
    {{-- info selection --}}
    <div class="flex">
        <div class="relative inline-block text-left w-full pr-4 lg:pr-0 md:pr-0">
            <div id="OrganizationSelector" class="w-full flex items-center">
                <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md">Application</label>
                <div class="selectWrapperLG w-full">
                        <select id="Applications" class="open-sans-soft-regular border-r-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] w-full text-white text-lg hover:bg-[#4a4a4a] p-6 pr-10 rounded-r-lg font-bold shadow-md" wire:change="$js.ChangeApplication($event,$event.target.value)">
                            @foreach($Applications as $application)
                                @if (isset($ApplicationInfo))
                                    @if ($application->application_id == $ApplicationInfo->application_id)
                                        <option selected value="{{ $application->application_id }}" id="{{ $application->application_id }}">{{ $application->application_name }}</option>
                                    @else
                                        <option value="{{ $application->application_id }}" id="{{ $application->application_id }}">{{ $application->application_name }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                </div>
            </div>
        </div>
    </div>
    <div class="lg:p-10 md:p-10 pb-15 pr-10 pl-2 pt-2 bg-white shadow-md mt-8 rounded-lg h-screen md:h-[645px] lg:h-[645px]">
        <span class="flex items-center justify-between mb-4">
            {{-- top half --}}
            {{-- refresh button --}}
            <span class="flex gap-4 items-center">
                <label class="text-[#1c648c] font-semibold text-3xl">Application Device Type Association </label>
                <button wire:click="$js.refresh" class="text-[#1c648c] text-5xl hover:bg-gray-100 rounded-lg hover:outline-hidden cursor-pointer p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" fill="#00719d" width="36px" height="36px">
                        <path id="Refresh" class="cls-1" d="M22.96,12.07c-.25-2.66-1.52-5.07-3.58-6.78-.04-.03-.08-.06-.12-.09-.44-.27-1.01-.21-1.39.14-.23.21-.36.5-.37.81-.01.31.1.6.31.83.03.03.06.06.09.08,1.06.88,1.87,2.02,2.34,3.32.7,1.93.6,4.02-.27,5.88-.87,1.86-2.42,3.27-4.35,3.96-4,1.44-8.42-.63-9.86-4.62-.44-1.23-.57-2.55-.36-3.84.56-3.47,3.37-6.01,6.7-6.4l-1.18,1.18c-.39.39-.39,1.02,0,1.41.2.2.45.29.71.29s.51-.1.71-.29l2.77-2.77s.01,0,.02,0c.03-.02.04-.05.06-.07l.15-.15s.04-.07.07-.1c0,0,.01-.01.01-.02.29-.39.28-.94-.08-1.29l-3-3c-.39-.39-1.02-.39-1.41,0-.39.39-.39,1.02,0,1.41l1.11,1.11c-3.48.35-6.59,2.49-8.1,5.68-.62,1.31-.94,2.78-.95,4.23,0,2.67,1.03,5.19,2.92,7.08s4.4,2.94,7.07,2.94h0c2.98,0,5.79-1.32,7.69-3.61,1.71-2.06,2.51-4.65,2.27-7.31Z"/>
                    </svg>
                </button>
            </span>
            {{-- export button --}}
            <button id="Export" wire:click="$js.DownloadCSV" class="export flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px" class="svg" fill="#46c0e5">
                    <path id="Export" class="cls-1" d="M16.71,13.62c.39.39.39,1.02,0,1.41-.2.2-.45.29-.71.29s-.51-.1-.71-.29l-1.29-1.29v3.93c0,.55-.45,1-1,1s-1-.45-1-1v-3.93l-1.29,1.29c-.39.39-1.02.39-1.41,0s-.39-1.02,0-1.41l3-3c.38-.38,1.04-.38,1.41,0l3,3ZM23,8v12.81c-.03,2.31-1.94,4.19-4.29,4.19H7.31s-.05,0-.06,0c-2.31,0-4.22-1.88-4.25-4.22V5.19c.03-2.31,1.94-4.19,4.25-4.19h.03s8.71,0,8.71,0c.53,0,1.04.21,1.41.59l5,5c.38.38.59.88.59,1.41ZM17,7h3l-3-3v3ZM21,9h-4c-1.1,0-2-.9-2-2v-4h-7.71s-.02,0-.03,0c-1.23,0-2.24.99-2.25,2.22v15.56c.02,1.23,1.02,2.22,2.25,2.22.01,0,.02,0,.03,0h11.43s.02,0,.03,0c1.23,0,2.24-.99,2.25-2.22v-11.78Z"/>
                </svg>
                EXPORT
            </button>
        </span>
        {{-- table section --}}
        <table class="rounded-lg border-2 border-[#f4f4f4] border-separate w-full block overflow-y-auto overflow-x-auto border-spacing-[0]">
            <thead class="rounded-lg bg-[#f2f2f2] border-2 border-[#f4f4f4] border-separate">
                <tr>
                    <th><input type="checkbox" wire:click="$js.SelectAll($event)"></th>
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
                    <td id="LoadingIcon" class="relative align-center h-[220px] hide" colspan="999" wire:loading.class.remove="hide">
                        <span class="absolute top-[35%] left-[41%]" wire:loading>
                            <img src="/images/Loading_2.gif">
                        </span>
                    </td>
            </tbody>
        </table>
        {{-- bottom section --}}
        <div class="flex justify-between lg:mt-4">
            {{-- delete button --}}
            <span>
                <button id="Delete" wire:click="$js.OpenDeleteModal">
                    <div id="DeleteFrame" class="bg-[#f2f2f2] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                        <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                            <path id="Delete" class="cls-1" d="M21.5,6h-4.5v-3c0-.55-.45-1-1-1h-6c-.55,0-1,.45-1,1v3h-4.5c-.55,0-1,.45-1,1s.45,1,1,1h.62l2.01,15.13c.07.5.49.87.99.87h9.76c.5,0,.93-.37.99-.87l2.01-15.13h.62c.55,0,1-.45,1-1s-.45-1-1-1ZM11,4h4v2h-4v-2ZM17,22h-8.01l-1.86-14h11.72l-1.86,14Z"/>
                        </svg>
                    </div>
                </button>
            </span>
            {{-- edit, add, and save to db button --}}
            <span class="flex gap-4 items-start">
                <button disabled id="Edit" wire:click="$js.CloseOpenEdit">
                    <div id="EditFrame" class="bg-[#f2f2f2] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                        <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                            <path id="Edit" class="cls-1" d="M24,23c0,.55-.45,1-1,1H3c-.55,0-1-.45-1-1s.45-1,1-1h20c.55,0,1,.45,1,1ZM6.61,19.79l-.21-3.44c-.02-.28.06-.55.21-.79L15.06,2.59c.37-.58,1.16-.77,1.76-.41.01,0,.03.02.04.02l3.45,2.23c.29.19.49.48.56.82.07.34,0,.7-.19.99l-8.44,12.96c-.15.23-.38.42-.65.51l-3.23,1.21c-.16.06-.31.08-.46.08h0c-.68,0-1.25-.54-1.29-1.21ZM8.41,16.45l.14,2.26,2.14-.8,7.94-12.18-2.27-1.47-7.94,12.19Z"/>
                        </svg>
                    </div>
                </button>
                <button id="Add" wire:click="$js.CloseOpenAdd">
                    <div id="AddFrame" class="bg-[#42bee4] flex items-center justify-center text-white rounded-full pb-2 pr-3 pl-3 text-5xl hover:bg-[#368fb3] cursor-pointer">
                        +
                    </div>
                </button>
                <button wire:click="$js.saveToDB" id="save" class="save flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                    <svg class="svg" stroke="#4fbce7" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <polyline data-name="Right" fill="none" id="Right-2" points="7.9 12.3 12 16.3 16.1 12.3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="2.7" y2="14.2"/>
                    </svg>
                    SAVE TO DATABASE
                </button>
            </span>
        </div>
    </div>
    </div>
    {{-- RIGHT SIDE --}}
    {{-- AddOption --}}
    <div id="AddMenu" class="hide relative transition-all duration-100">
        {{-- top of add --}}
        <button id="Add">
            <div class="absolute">
                <div class="absolute left-[-25px] top-37 lg:z-3 md:z-3 bg-[#42bee4] flex items-center justify-center text-white rounded-full pb-2 pr-3 pl-3 text-5xl">
                    +
                </div>
            </div>
        </button>
        <div class="text-white bg-[#00719d] z-2 top-28 rounded-t-lg absolute w-[383px] h-[120px] text-start pl-10">
            <h1 class="absolute top-14">Add Row</h1>       
        </div>
        <div class="absolute text-white lg:right-18 left-85 top-30">
            <button type="button" wire:click="$js.CloseOpenAdd" class="absolute z-2 hover:bg-[#015c80] p-2 rounded-lg cursor-pointer text-2xl">✕</button>
        </div>
        {{-- form --}}  
        <form>
            <div id="AddAssoc" class="pt-24 pb-30 relative bg-[#00719d] z-1 pl-10 pt-1 pr-3 mt-22 text-white h-[645px] rounded-lg w-[400px] overflow-x-visible overflow-y-scroll">
                    <livewire:components.frm-select-box selectMessage="Device Type:" id="DeviceType" key="{{ Str::random() }}" optionName="device_type" optionId="device_type_id" :options="$DeviceTypeInfo"></livewire:components.frm-select-box>
                    <livewire:components.underline-input id="description" placeholder="Description" type="text"></livewire:components.underline-input>
            </div>
            {{-- Confirm Section --}}
            <div class="absolute z-2 text-white left-0 top-160 w-[382px] bg-[#00719d] p-4 h-[116px] rounded-b-lg">
                    <button id="AddConfirm" type="submit" wire:click="$js.AddConfirm($event)" class="absolute left-22 top-8 bg-white text-[#74bec9] p-4 rounded-full font-semibold pl-20 pr-20 cursor-pointer hover:bg-neutral-100">CONFIRM</button>
            </div>
        </form>
    </div>
    {{-- Edit Option --}}
    <div id="EditMenu" class="hide relative transition-all duration-100">
        {{-- top of edit --}}
        <button id="Edit">
            <div class="absolute">
                <div class="absolute left-[-25px] top-37 z-3 bg-[#42bee4] flex items-center justify-center text-white rounded-full pb-4 pr-4 pl-4 pt-4 text-5xl">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                        <path id="Edit" class="cls-1" d="M24,23c0,.55-.45,1-1,1H3c-.55,0-1-.45-1-1s.45-1,1-1h20c.55,0,1,.45,1,1ZM6.61,19.79l-.21-3.44c-.02-.28.06-.55.21-.79L15.06,2.59c.37-.58,1.16-.77,1.76-.41.01,0,.03.02.04.02l3.45,2.23c.29.19.49.48.56.82.07.34,0,.7-.19.99l-8.44,12.96c-.15.23-.38.42-.65.51l-3.23,1.21c-.16.06-.31.08-.46.08h0c-.68,0-1.25-.54-1.29-1.21ZM8.41,16.45l.14,2.26,2.14-.8,7.94-12.18-2.27-1.47-7.94,12.19Z"/>
                    </svg>
                </div>
            </div>
        </button>
        <div class="text-white bg-[#00719d] z-2 top-28 rounded-t-lg absolute w-[383px] h-[120px] text-start pl-10">
            <h1 class="absolute top-14">Edit Row</h1>       
        </div>
        <div class="absolute text-white right-18 top-30">
            <button type="button" wire:click="$js.CloseOpenEdit" class="absolute z-2 hover:bg-[#015c80] p-2 rounded-lg cursor-pointer text-2xl">✕</button>
        </div>
        {{-- form --}}  
        <form>
            <div id="EditAssoc" class="pt-24 pb-30 relative bg-[#00719d] z-1 pl-10 pt-1 pr-3 mt-22 text-white h-[645px] rounded-lg w-[400px] overflow-x-visible overflow-y-scroll">
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Application:</label>
                    <b class="text-lg" id="application"></b>
                </div>
                <div class="mt-4 pl-2 text-lg flex flex-col">
                    <label>device Type: </label>
                    <b class="text-lg" id="DeviceType"></b>
                </div>
                <div class="mt-4 pl-2 text-lg flex flex-col">
                    <label>Creation Time: </label>
                    <b class="text-lg" id="creationTime"></b>
                </div>
                <div class="mt-4 pl-2 text-lg flex flex-col">
                    <label>Created By: </label>
                    <b class="text-lg" id="createdBy"></b>
                </div>
                <livewire:components.underline-input id="description" placeholder="Description" type="text"></livewire:components.underline-input>
            </div>
            {{-- Confirm Section --}}
            <div class="absolute z-2 text-white left-0 top-160 w-[382px] bg-[#00719d] p-4 h-[116px] rounded-b-lg">
                <button type="submit" wire:click="$js.EditConfirm($event)" id="EditConfirm" class="absolute left-22 top-8 bg-white text-[#74bec9] p-4 rounded-full font-semibold pl-20 pr-20 cursor-pointer hover:bg-neutral-100">CONFIRM</button>
            </div>
        </form>
    </div>
    <livewire:modals.confirm-delete-modal key="{{ Str::random() }}"></livewire:modals.confirm-delete-modal>
    <livewire:alert.notification key="{{ Str::random() }}"></livewire:alert.notification>
</div>
        @script
        <script>
            
            let ItemsSelected = [];

            let AddMenuStatus = false;
            let EditMenuStatus = false;
            let DeleteMenuStatus = false;
            let EditItem = ""; //used to pre-populate an edit
            let headers = $wire.headers;
            let application = "";
            let user = $wire.user;
            let ActionsDone = [];
            let TableObjects = [];
            function EnableDisableEditDelete(){
                console.log(ItemsSelected);
                if (ItemsSelected.length == 1){
                    //enable
                    $("#EditFrame").removeClass("bg-[#f2f2f2]");
                    $("#Edit").prop("disabled",false);
                    $("#EditFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    EditItem = ItemsSelected[0];
                    

                    //enable
                    $("#DeleteFrame").removeClass("bg-[#f2f2f2]");
                    $("#Delete").prop("disabled",false);
                    $("#DeleteFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                }
                else if (ItemsSelected.length > 1){
                    //disable
                    $("#EditFrame").addClass("bg-[#f2f2f2]");
                    $("#Edit").prop("disabled",true);
                    $("#EditFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    if (EditMenuStatus == false){
                        EditItem = "";
                    }

                    //enable
                    $("#DeleteFrame").removeClass("bg-[#f2f2f2]");
                    $("#Delete").prop("disabled",false);
                    $("#DeleteFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                }
                else{
                    //full disable
                    $("#EditFrame").addClass("bg-[#f2f2f2]");
                    $("#Edit").prop("disabled",true);
                    $("#EditFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    if (EditMenuStatus == false){
                        EditItem = "";
                    }

                    $("#DeleteFrame").addClass("bg-[#f2f2f2]");
                    $("#Delete").prop("disabled",true);
                    $("#DeleteFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                }
            }
            //select box stuff 
            $js('ItemChecked',function(e,id){
                if (e.target.type == "checkbox"){
                    if (e.target.checked == true){
                        ItemsSelected.push(id);
                        $("#"+SpaceToUnderScore(id)).addClass("bg-[#f8c200]");
                        closeEditMenu();
                        closeAddMenu();
                        EnableDisableEditDelete();
                    }
                    else{
                        let index = ItemsSelected.indexOf(id);
                        $("#"+SpaceToUnderScore(id)).removeClass("bg-[#f8c200]");
                        ItemsSelected.splice(index,1);
                        closeEditMenu();
                        closeAddMenu();
                        EnableDisableEditDelete();
                    }
                }
            });

            $js("SelectAll",function(e){
                if (e.target.checked){
                    let CheckBoxes = $("tbody input[type='checkbox']");

                    for (let i = 0; i < CheckBoxes.length; i++){
                        if (CheckBoxes[i].checked == false){
                            //converting json to object
                            let obj = $(CheckBoxes[i].parentNode.parentNode).attr("id")
                            ItemsSelected.push(obj);
                        }
                        CheckBoxes[i].checked = true
                        $(CheckBoxes[i].parentNode.parentNode).addClass("bg-[#f8c200]");
                    }
                    closeEditMenu();
                    closeAddMenu();
                    EnableDisableEditDelete();
                }
                else{
                    let CheckBoxes = $("tbody input[type='checkbox']");

                    for (let i = 0; i < CheckBoxes.length; i++){
                        if (CheckBoxes[i].checked == true){
                            ItemsSelected.splice(-1,1);
                        }
                        CheckBoxes[i].checked = false
                        $(CheckBoxes[i].parentNode.parentNode).removeClass("bg-[#f8c200]");
                    }
                    closeEditMenu();
                    closeAddMenu();
                    EnableDisableEditDelete();
                }
            });

            //used to make sure the primary key being added is a unique key
            function ValidateIfUnique(ID, Mode){

                let result = "";
                let IDDupeCount = 0;
                let IDAsName = $("option[id='"+ID.toString().trim()+"']").val();
                $("#InfoTable").children().each(function(index){
                    if ($(this).children()[3] !== undefined){
                        let id = $(this).children()[3].textContent;
                        if (id.toString() == IDAsName.toString()){
                        IDDupeCount+=1
                    }
                    }
                });
                if (Mode == "add"){
                    if (IDDupeCount >= 1){
                        return "Application device associatian already exists";
                    }
                }
                else if (Mode == "edit"){
                    if (IDDupeCount > 1){
                        return "Application device associatian already exists";
                    }
                }
                return "";
            }
            //used for adding items
            $js("AddConfirm",function(e){
                
                e.preventDefault();
                let FormVals = PopulateArrayWithVals("AddAssoc");
                let result = ValidateIfUnique(FormVals[0],"add");
                if (result != ""){
                    setAlertText(result);
                    displayAlert();
                    return;
                }

                //chk
                let tr = document.createElement("tr");
                tr.id=SpaceToUnderScore(FormVals[0]);
                let checkboxTD = document.createElement("td")
                checkboxTD.innerHTML = "<input type='checkbox' wire:click=\"$js.ItemChecked($event,'"+FormVals[0]+"')\">"
                tr.appendChild(checkboxTD);

                //sequence
                let SequenceTD = document.createElement("td");
                SequenceTD.textContent = ($("#InfoTable").children().length + 1)
                tr.appendChild(SequenceTD);

                //normal vals
                FormVals.forEach(function(value,index){
                    if (index == 0){
                        let td2 = document.createElement("td");
                        td2.textContent = application;
                        tr.appendChild(td2);
                        //putting value of option instead of id
                        let td = document.createElement("td");
                        td.textContent = $("option[id='"+value.toString().trim()+"']").val();
                        tr.appendChild(td)
                    }
                    else if (index == 1){
                        //just appending current date plus who made Application device assoc
                        let td2 = document.createElement("td");
                        td2.textContent = CurrentDateTimeAsString();
                        tr.appendChild(td2);
                        let td3 = document.createElement("td");
                        td3.textContent = user["user_username"];
                        tr.appendChild(td3);
                        let td = document.createElement("td");
                        td.textContent = value.toString().trim();
                        tr.appendChild(td)
                    }
                })
                ActionsDone.push("INSERT~!~"+JSON.stringify(TRToObject($(tr))));
                console.log(ActionsDone);
                $("#InfoTable").append(tr);
                setAlertText("Successfully added application device type association");
                displayAlert();
                closeAddMenu()
            });
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
            function PopulateArrayWithVals(EditAdd){
                let FormVals = [];
                FormVals.push($(`#${EditAdd} #DeviceType`).find('option:selected').attr("id"));
                FormVals.push($(`#${EditAdd} #description`).val());
                return FormVals;
            }
            //used for editing
            $js("EditConfirm",function(e){
                if ($("#EditAssoc #applicationName").val() == ""){
                    return;
                }
                e.preventDefault();
                let FormVals = PopulateArrayWithVals("EditAssoc");
                let OGCopy = $("#"+SpaceToUnderScore(EditItem)).clone(false);
                $("#"+SpaceToUnderScore(EditItem)).children().each(function(index){
                    //we exclude the checkbox, sequence num, exclude org name
                    if (index >=6){
                        $(this).text(FormVals[FormVals.length-1]);
                    }
                });
                
                Result = ""; //setting to default since no need to validate for uniqueness
                if (Result != ""){
                    $(OGCopy).insertAfter($("#"+SpaceToUnderScore(EditItem)));
                    $("#"+SpaceToUnderScore(EditItem)).remove();
                    setAlertText(Result);
                    displayAlert();
                }
                else{
                    ActionsDone.push("UPDATE["+EditItem+"]~!~"+JSON.stringify(TRToObject($("#"+SpaceToUnderScore(EditItem)))))
                    console.log(ActionsDone);
                    setTimeout(function(){
                        $("#"+SpaceToUnderScore(EditItem)).children().first().children().click(); //clicks the checkbox
                    },100);
                    //now we close the menu
                    setAlertText("Successfully updated application device type association");
                    displayAlert();
                    closeEditMenu();
                }
            });
            function closeEditMenu(){
                EditMenuStatus = false;
                $("#EditMenu").addClass("opacity-0");
                setTimeout(function(){
                    $("#EditMenu").addClass("hide");
                },110);       
            }
            function closeAddMenu(){
                AddMenuStatus = false;
                $("#AddMenu").addClass("opacity-0");
                setTimeout(function(){
                    $("#AddMenu").addClass("hide");
                },110);
            }
            //used to open and close the add menu
            $js("CloseOpenAdd",function(){
                if (EditMenuStatus == true || DeleteMenuStatus == true){
                    return;
                }
                if (AddMenuStatus == false){
                    AddMenuStatus = true;
                    $("#AddMenu").removeClass("hide");
                    $("#AddMenu").removeClass("opacity-0");
                }
                else{
                    closeAddMenu();     
                }
            });
            //used to open and close edit menu
            $js("CloseOpenEdit",function(){
                if (AddMenuStatus == true || DeleteMenuStatus == true){
                    return;
                }
                if (EditMenuStatus == false){
                    EditMenuStatus = true;
                    $("#EditMenu").removeClass("hide");
                    $("#EditMenu").removeClass("opacity-0");
                    let Obj = TRToObject($("#"+SpaceToUnderScore(EditItem)));
                    $("#EditAssoc #application").text(Obj["APPLICATION"]);
                    $("#EditAssoc #DeviceType").text(Obj["DEVICE TYPE"]);
                    $("#EditAssoc #creationTime").text(Obj["CREATION TIME"]);
                    $("#EditAssoc #createdBy").text(Obj["CREATED BY"]);
                    $("#EditAssoc #description").val(Obj["DESCRIPTION"]);
                }
                else{
                    closeEditMenu();
                }
            });
            $js("OpenDeleteModal",async function(){
                if (EditMenuStatus == true || AddMenuStatus == true){
                    return;
                }
                let name = $("#"+SpaceToUnderScore(ItemsSelected[0])).children()[3].innerHTML;
                if (ItemsSelected.length == 1){
                    $("#DeleteMessage").text("Are you sure you want to delete,")
                    $("#ItemToDelete").text(name);
                    $("#DeleteModal").removeClass("hide");
                    OpenDeleteModal();
                }
                else{
                    $("#DeleteMessage").text("Are you sure you want to delete the " + ItemsSelected.length + " items selected?");
                    $("#ItemToDelete").text("");
                    $("#DeleteModal").removeClass("hide");
                    OpenDeleteModal();
                }
            });
            function OpenDeleteModal(){
                setTimeout(function(){
                    $("#DeleteModalFrame").removeClass("opacity-0 ease-in duration-200");
                    $("#DeleteModalFrame").addClass("opacity-100 ease-out duration-300");
                    $("#DeleteModalMain").removeClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
                    $("#DeleteModalMain").addClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95 duration-300");
                },50)
            }
            function CloseDeleteModal(){
                $("#DeleteModalFrame").addClass("opacity-0 ease-in duration-200");
                $("#DeleteModalFrame").removeClass("opacity-100 ease-out duration-300");
                $("#DeleteModalMain").addClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
                $("#DeleteModalMain").removeClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95");
            }
            $js("refresh",refresh)
            async function refresh(){
                ShowLoading()
                //reset actions done
                ActionsDone = [];
                //uncheck everything
                let ItemsToUnCheck = []; //need seperate array to remove all items from array once they are removed from table
                ItemsSelected.forEach(function(item){
                    ItemsToUnCheck.push(item);
                });
                ItemsToUnCheck.forEach(function(item){
                    $("#"+SpaceToUnderScore(item)).children().first().children().click();
                })
                ItemsSelected = [];
                //update buttons
                EnableDisableEditDelete();

                //now that everything is unchecked we re-load the table and org
                await $wire.call("LoadInfo");
                //re-gen sequence nums
                try{
                    $("#InfoTable").children().each(function(index){
                        $(this).children()[1].textContent = index+1;
                    })
                }
                catch(e){

                }
                PrepFileForExport();
                //re-set confirm delete listener
                $("#ConfirmDelete").click(function(e){
                    let ItemsToDelete = []; //need seperate array to remove all items from array once they are removed from table
                    ItemsSelected.forEach(function(item){
                        ItemsToDelete.push(item);
                    });
                    ItemsToDelete.forEach(function(item){
                        $("#"+SpaceToUnderScore(item)).children().first().children().click();
                        $("#"+SpaceToUnderScore(item)).remove();
                    })
                    ActionsDone.push("DELETE~!~"+ItemsToDelete);
                    CloseDeleteModal();
                    setTimeout(function(){
                        $("#DeleteModal").addClass("hide");
                    },200);
                    setAlertText("Successfully deleted device type associations");
                    displayAlert();
                });
                closeAddMenu();
                closeEditMenu();
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
            function ShowLoading(){
                let LoadingTD = $("#InfoTable #LoadingIcon");
                LoadingTD.html(
                    "<span class=\"absolute top-[35%] left-[41%]\" wire:loading colspan=\"999\"><img src=\"/images/Loading_2.gif\"></span>"
                )
                $("#InfoTable").text(""); //clearing current Info
                $("#InfoTable").append(LoadingTD);
            }
            $js("saveToDB",async function(ev){
                ShowLoading()
                let Result = await $wire.call("SaveToDb",JSON.stringify(ActionsDone));
                let Errors = false;
                let ErrorMsg = "";
                ActionsDone.forEach(function(item,index){
                    let InfoArray = item.split("~!~");
                    let Type = InfoArray[0];
                    let ItemInfo = InfoArray[1];

                    try{
                        if (Type.includes("UPDATE")){
                            if (Result[index] == 0){
                                Errors = true;
                                let Obj = JSON.parse(ItemInfo);
                                ErrorMsg += "Failed to update association for\"" + Obj["DEVICE TYPE"] + "\"<br>";
                            }
                        }
                        else if (Type.includes("INSERT")){
                            if (Result[index] != true){
                                Errors = true;
                                let Obj = JSON.parse(ItemInfo);
                                ErrorMsg += "Failed to insert association for\"" + Obj["DEVICE TYPE"] + "\"<br>";
                            }
                        }
                        else if (Type.includes("DELETE")){
                            if (Result[index] == 0){
                                Errors = true;
                                ErrorMsg += "Failed to delete Associations(s) " + ItemInfo + "<br>";
                            }
                        }
                    }
                    catch(ex){
                        Errors = true;
                        ErrorMsg += "Failed to save to database";
                    }
                });
                await refresh();
                if (Errors == true){
                    setAlertText(ErrorMsg);
                    displayAlert();
                }
                else{
                    setAlertText("Saved to database");
                    displayAlert();
                }
            })
            $js("ChangeApplication",async function(ev,Application){
                ShowLoading()
                await $wire.call("SetApplication",Application)
                application = $wire.application;
                await refresh();
            })
            //generate Sequence Numbers on load ------------------------------------------------------------------------ON LOAD SEGMENT---------------------------
            $(document).ready(async function(){
                await $wire.call("LoadApplications");
                await $wire.call("setDefaultApplication");
                application = $wire.application;
                await refresh();
                EnableDisableEditDelete();
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
                    let result = exportToCsv("Application-DeviceTypeAssocInfo.csv",TableObjects);
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