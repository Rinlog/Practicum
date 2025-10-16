<div id="MainWindowSettings" class="flex flex-col lg:flex-row lg:w-[1750px] gap-10">
    @if ($this->Perms['read'] == false)
        <script>window.location = "/home";</script>
    @endif
    {{-- LEFT SIDE --}}
    <div class="relative w-[90%] md:w-[80%] lg:w-[70%]">
        <div class="flex">
            <div class="relative inline-block text-left w-full pr-4 lg:pr-0 md:pr-0">
                <div id="OrganizationSelector" class="w-full flex items-center lg:flex-row md:flex-row flex-col">
                    <div class="flex w-full">
                        <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md w-[100%] md:w-[100%] lg:w-[40%] whitespace-nowrap">Component Name</label>
                        <div class="selectWrapperLG w-full">
                        <select id="Components" class="open-sans-soft-regular border-r-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] text-white text-lg hover:bg-[#4a4a4a] w-full md:p-6 lg:p-6 p-6 pr-10 rounded-r-lg font-bold shadow-md" wire:change="$js.ChangeComponent($event,$event.target.value)">
                            @foreach ($Components as $Component)
                                @if (isset($ComponentInfo))
                                    @if($Component->component_id == $ComponentInfo->component_id)
                                        <option value="{{ $Component->component_id }}" selected >{{ $Component->component_name }}</option>
                                    @else
                                        <option value="{{ $Component->component_id }}">{{ $Component->component_name }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="flex w-full">
                        <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md w-[100%] md:w-[100%] lg:w-[40%] whitespace-nowrap">Applications</label>
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
        </div>
    {{-- info selection --}}
    <div class="flex">
        <div class="relative inline-block text-left w-full pr-4 lg:pr-0 md:pr-0">
        </div>
    </div>
    <div class="lg:p-10 md:p-10 pb-10 pr-10 pl-2 pt-2 bg-white shadow-md mt-8 rounded-lg h-screen md:h-[645px] lg:h-[645px]">
        <span class="flex items-center justify-between mb-4">
            {{-- top half --}}
            {{-- refresh button --}}
            <span class="flex gap-4 items-center">
                <label class="text-[#1c648c] font-semibold text-3xl">Api Access Information</label>
                <button wire:click="$js.refresh" class="text-[#1c648c] text-5xl hover:bg-gray-100 rounded-lg hover:outline-hidden cursor-pointer p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" fill="#00719d" width="36px" height="36px">
                        <path id="Refresh" class="cls-1" d="M22.96,12.07c-.25-2.66-1.52-5.07-3.58-6.78-.04-.03-.08-.06-.12-.09-.44-.27-1.01-.21-1.39.14-.23.21-.36.5-.37.81-.01.31.1.6.31.83.03.03.06.06.09.08,1.06.88,1.87,2.02,2.34,3.32.7,1.93.6,4.02-.27,5.88-.87,1.86-2.42,3.27-4.35,3.96-4,1.44-8.42-.63-9.86-4.62-.44-1.23-.57-2.55-.36-3.84.56-3.47,3.37-6.01,6.7-6.4l-1.18,1.18c-.39.39-.39,1.02,0,1.41.2.2.45.29.71.29s.51-.1.71-.29l2.77-2.77s.01,0,.02,0c.03-.02.04-.05.06-.07l.15-.15s.04-.07.07-.1c0,0,.01-.01.01-.02.29-.39.28-.94-.08-1.29l-3-3c-.39-.39-1.02-.39-1.41,0-.39.39-.39,1.02,0,1.41l1.11,1.11c-3.48.35-6.59,2.49-8.1,5.68-.62,1.31-.94,2.78-.95,4.23,0,2.67,1.03,5.19,2.92,7.08s4.4,2.94,7.07,2.94h0c2.98,0,5.79-1.32,7.69-3.61,1.71-2.06,2.51-4.65,2.27-7.31Z"/>
                    </svg>
                </button>
            </span>
            @if ($Perms["report"] == true)
                {{-- export button --}}
                <button id="Export" wire:click="$js.DownloadCSV" class="export flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px" class="svg" fill="#46c0e5">
                        <path id="Export" class="cls-1" d="M16.71,13.62c.39.39.39,1.02,0,1.41-.2.2-.45.29-.71.29s-.51-.1-.71-.29l-1.29-1.29v3.93c0,.55-.45,1-1,1s-1-.45-1-1v-3.93l-1.29,1.29c-.39.39-1.02.39-1.41,0s-.39-1.02,0-1.41l3-3c.38-.38,1.04-.38,1.41,0l3,3ZM23,8v12.81c-.03,2.31-1.94,4.19-4.29,4.19H7.31s-.05,0-.06,0c-2.31,0-4.22-1.88-4.25-4.22V5.19c.03-2.31,1.94-4.19,4.25-4.19h.03s8.71,0,8.71,0c.53,0,1.04.21,1.41.59l5,5c.38.38.59.88.59,1.41ZM17,7h3l-3-3v3ZM21,9h-4c-1.1,0-2-.9-2-2v-4h-7.71s-.02,0-.03,0c-1.23,0-2.24.99-2.25,2.22v15.56c.02,1.23,1.02,2.22,2.25,2.22.01,0,.02,0,.03,0h11.43s.02,0,.03,0c1.23,0,2.24-.99,2.25-2.22v-11.78Z"/>
                    </svg>
                    EXPORT
                </button>
            @else
                {{-- disabled export button --}}
                <button disabled id="DisabledExport" class=" flex text-[#dddddd] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" viewBox="0 0 26 26" width="24px" height="24px" class="svg" fill="#dddddd">
                        <path id="Export" class="cls-1" d="M16.71,13.62c.39.39.39,1.02,0,1.41-.2.2-.45.29-.71.29s-.51-.1-.71-.29l-1.29-1.29v3.93c0,.55-.45,1-1,1s-1-.45-1-1v-3.93l-1.29,1.29c-.39.39-1.02.39-1.41,0s-.39-1.02,0-1.41l3-3c.38-.38,1.04-.38,1.41,0l3,3ZM23,8v12.81c-.03,2.31-1.94,4.19-4.29,4.19H7.31s-.05,0-.06,0c-2.31,0-4.22-1.88-4.25-4.22V5.19c.03-2.31,1.94-4.19,4.25-4.19h.03s8.71,0,8.71,0c.53,0,1.04.21,1.41.59l5,5c.38.38.59.88.59,1.41ZM17,7h3l-3-3v3ZM21,9h-4c-1.1,0-2-.9-2-2v-4h-7.71s-.02,0-.03,0c-1.23,0-2.24.99-2.25,2.22v15.56c.02,1.23,1.02,2.22,2.25,2.22.01,0,.02,0,.03,0h11.43s.02,0,.03,0c1.23,0,2.24-.99,2.25-2.22v-11.78Z"/>
                    </svg>
                    EXPORT
                </button>
            @endif
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
                        <span class="absolute top-[35%] left-[38%]" wire:loading>
                            <img src="/images/Loading_2.gif">
                        </span>
                    </td>
            </tbody>
        </table>
        {{-- bottom section --}}
        <div class="flex justify-between lg:mt-4">
            @if ($Perms["delete"] == true)
                {{-- delete button --}}
                <span>
                    <button id="Delete" wire:click="$js.OpenDeleteModal">
                        <div id="DeleteFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                                <path id="Delete" class="cls-1" d="M21.5,6h-4.5v-3c0-.55-.45-1-1-1h-6c-.55,0-1,.45-1,1v3h-4.5c-.55,0-1,.45-1,1s.45,1,1,1h.62l2.01,15.13c.07.5.49.87.99.87h9.76c.5,0,.93-.37.99-.87l2.01-15.13h.62c.55,0,1-.45,1-1s-.45-1-1-1ZM11,4h4v2h-4v-2ZM17,22h-8.01l-1.86-14h11.72l-1.86,14Z"/>
                            </svg>
                        </div>
                    </button>
                </span>
            @else
                {{-- delete button --}}
                <span>
                    <button disabled id="DisabledDelete">
                        <div id="DisabledDeleteFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                                <path id="Delete" class="cls-1" d="M21.5,6h-4.5v-3c0-.55-.45-1-1-1h-6c-.55,0-1,.45-1,1v3h-4.5c-.55,0-1,.45-1,1s.45,1,1,1h.62l2.01,15.13c.07.5.49.87.99.87h9.76c.5,0,.93-.37.99-.87l2.01-15.13h.62c.55,0,1-.45,1-1s-.45-1-1-1ZM11,4h4v2h-4v-2ZM17,22h-8.01l-1.86-14h11.72l-1.86,14Z"/>
                            </svg>
                        </div>
                    </button>
                </span>
            @endif
            {{-- edit, add, and save to db button --}}
            <span class="flex gap-4 items-start">
                @if ($Perms["create"] == true || $Perms["update"] == true)
                    <button disabled id="Edit" wire:click="$js.CloseOpenEdit">
                        <div id="EditFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                                <path id="Edit" class="cls-1" d="M24,23c0,.55-.45,1-1,1H3c-.55,0-1-.45-1-1s.45-1,1-1h20c.55,0,1,.45,1,1ZM6.61,19.79l-.21-3.44c-.02-.28.06-.55.21-.79L15.06,2.59c.37-.58,1.16-.77,1.76-.41.01,0,.03.02.04.02l3.45,2.23c.29.19.49.48.56.82.07.34,0,.7-.19.99l-8.44,12.96c-.15.23-.38.42-.65.51l-3.23,1.21c-.16.06-.31.08-.46.08h0c-.68,0-1.25-.54-1.29-1.21ZM8.41,16.45l.14,2.26,2.14-.8,7.94-12.18-2.27-1.47-7.94,12.19Z"/>
                            </svg>
                        </div>
                    </button>
                @else
                    <button disabled id="DisabledEdit">
                        <div id="DisabledEditFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full pb-4 pt-4 pr-4 pl-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                                <path id="Edit" class="cls-1" d="M24,23c0,.55-.45,1-1,1H3c-.55,0-1-.45-1-1s.45-1,1-1h20c.55,0,1,.45,1,1ZM6.61,19.79l-.21-3.44c-.02-.28.06-.55.21-.79L15.06,2.59c.37-.58,1.16-.77,1.76-.41.01,0,.03.02.04.02l3.45,2.23c.29.19.49.48.56.82.07.34,0,.7-.19.99l-8.44,12.96c-.15.23-.38.42-.65.51l-3.23,1.21c-.16.06-.31.08-.46.08h0c-.68,0-1.25-.54-1.29-1.21ZM8.41,16.45l.14,2.26,2.14-.8,7.94-12.18-2.27-1.47-7.94,12.19Z"/>
                            </svg>
                        </div>
                    </button>
                @endif
                @if ($Perms["read"] == true)
                    <button disabled id="Inspect" wire:click="$js.CloseOpenInspect">
                        <div id="InspectFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full py-4 px-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 30 30" stroke="#FFFFFF" fill="#FFFFFF">
                                <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                            </svg>
                        </div>
                    </button>
                @else
                    <button disabled id="DisabledInspect">
                        <div id="DisabledInspectFrame" class="bg-[#dddddd] flex items-center justify-center text-white rounded-full py-4 px-4 text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 30 30" stroke="#FFFFFF" fill="#FFFFFF">
                                <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                            </svg>
                        </div>
                    </button>
                @endif
                @if (session()->get("SaveToDB") == true)
                    <button wire:click="$js.saveToDB" id="save" class="save flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                        <svg class="svg" stroke="#4fbce7" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <polyline data-name="Right" fill="none" id="Right-2" points="7.9 12.3 12 16.3 16.1 12.3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="2.7" y2="14.2"/>
                        </svg>
                        SAVE TO DATABASE
                    </button>
                @else
                    <button disabled  id="save" class=" flex text-[#dddddd] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center">
                        <svg class="svg" stroke="#dddddd" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <polyline data-name="Right" fill="none" id="Right-2" points="7.9 12.3 12 16.3 16.1 12.3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="2.7" y2="14.2"/>
                        </svg>
                        SAVE TO DATABASE
                    </button>
                @endif
            </span>
        </div>
    </div>
    </div>
    {{-- RIGHT SIDE --}}
    {{-- Edit Option --}}
    <div id="EditMenu" class="hide relative transition-all duration-100">
        {{-- top of edit --}}
        <button id="Edit">
            <div class="absolute">
                <div class="absolute left-[-25px] top-17 z-3 bg-[#42bee4] flex items-center justify-center text-white rounded-full pb-4 pr-4 pl-4 pt-4 text-5xl">
                    <svg xmlns="http://www.w3.org/2000/svg" id="" width="24px" height="24px" fill="#FFFFFF" viewBox="0 0 26 26">
                        <path id="Edit" class="cls-1" d="M24,23c0,.55-.45,1-1,1H3c-.55,0-1-.45-1-1s.45-1,1-1h20c.55,0,1,.45,1,1ZM6.61,19.79l-.21-3.44c-.02-.28.06-.55.21-.79L15.06,2.59c.37-.58,1.16-.77,1.76-.41.01,0,.03.02.04.02l3.45,2.23c.29.19.49.48.56.82.07.34,0,.7-.19.99l-8.44,12.96c-.15.23-.38.42-.65.51l-3.23,1.21c-.16.06-.31.08-.46.08h0c-.68,0-1.25-.54-1.29-1.21ZM8.41,16.45l.14,2.26,2.14-.8,7.94-12.18-2.27-1.47-7.94,12.19Z"/>
                    </svg>
                </div>
            </div>
        </button>
        <div class="text-white bg-[#00719d] z-2 top-8 rounded-t-lg absolute w-[383px] h-[120px] text-start pl-10">
            <h1 class="absolute top-14">Edit Token</h1>       
        </div>
        <div class="absolute text-white right-18 top-10">
            <button type="button" wire:click="$js.CloseOpenEdit" class="absolute z-2 hover:bg-[#015c80] p-2 rounded-lg cursor-pointer text-2xl">✕</button>
        </div>
        {{-- form --}}  
        <form>
            <div id="EditToken" class="pt-24 pb-30 relative bg-[#00719d] z-1 pl-10 pt-1 pr-3 mt-2 text-white h-[640px] rounded-lg w-[400px] overflow-x-visible overflow-y-scroll">
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Username:</label>
                    <b class="text-lg" id="userName"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Application:</label>
                    <b class="text-lg" id="application"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Role:</label>
                    <b class="text-lg" id="role"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Current Api Token:</label>
                    <b class="text-lg wrap-anywhere" id="cApiToken"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>New Api Token:</label>
                    <b class="text-lg wrap-anywhere" id="nApiToken"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Creation Time:</label>
                    <b class="text-lg" id="creationTime"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Created By:</label>
                    <b class="text-lg" id="createdBy"></b>
                </div>
                <div class="mt-6 pl-2 text-lg flex flex-col">
                    <label>Description:</label>
                    <b class="text-lg wrap-anywhere" id="description"></b>
                </div>
            </div>
            {{-- Confirm Section --}}
            <div class="absolute z-2 text-white left-0 top-140 w-[382px] bg-[#00719d] p-4 h-[116px] rounded-b-lg">
                <button type="submit" wire:click="$js.EditConfirm($event)" id="EditConfirm" class="absolute left-22 top-8 bg-white text-[#74bec9] p-4 rounded-full font-semibold pl-20 pr-20 cursor-pointer hover:bg-neutral-100">CONFIRM</button>
            </div>
        </form>
    </div>
    <livewire:modals.confirm-delete-modal key="{{ Str::random() }}"></livewire:modals.confirm-delete-modal>
    <livewire:alert.notification key="{{ Str::random() }}"></livewire:alert.notification>
    <livewire:modals.display-details-modal apiPage="true" key="{{ Str::random() }}"></livewire:modals.display-details-modal>
</div>
        @script
        <script>
            
            let ItemsSelected = [];

            let EditMenuStatus = false;
            let DeleteMenuStatus = false;
            let EditItem = ""; //used to pre-populate an edit
            let headers = $wire.headers;
            let ActionsDone = [];
            let TableObjects = [];
            function EnableDisableEditDelete(){
                console.log(ItemsSelected);
                if (ItemsSelected.length == 1){
                    //enable
                    $("#EditFrame").removeClass("bg-[#dddddd]");
                    $("#Edit").prop("disabled",false);
                    $("#EditFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    EditItem = ItemsSelected[0];
                    
                    
                    if (ValidToken(ItemsSelected)){
                        //enable
                        $("#DeleteFrame").removeClass("bg-[#dddddd]");
                        $("#Delete").prop("disabled",false);
                        $("#DeleteFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                        
                        //enable
                        $("#InspectFrame").removeClass("bg-[#dddddd]");
                        $("#Inspect").prop("disabled",false);
                        $("#InspectFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    }
                }
                else if (ItemsSelected.length > 1){
                    //disable
                    $("#EditFrame").addClass("bg-[#dddddd]");
                    $("#Edit").prop("disabled",true);
                    $("#EditFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    if (EditMenuStatus == false){
                        EditItem = "";
                    }
                    //disable
                    $("#InspectFrame").addClass("bg-[#dddddd]");
                    $("#Inspect").prop("disabled",true);
                    $("#InspectFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");

                    if (ValidToken(ItemsSelected)){
                        //enable
                        $("#DeleteFrame").removeClass("bg-[#dddddd]");
                        $("#Delete").prop("disabled",false);
                        $("#DeleteFrame").addClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    }
                    else{
                        $("#DeleteFrame").addClass("bg-[#dddddd]");
                        $("#Delete").prop("disabled",true);
                        $("#DeleteFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    }
                }
                else{
                    //full disable
                    $("#EditFrame").addClass("bg-[#dddddd]");
                    $("#Edit").prop("disabled",true);
                    $("#EditFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                    if (EditMenuStatus == false){
                        EditItem = "";
                    }

                    $("#DeleteFrame").addClass("bg-[#dddddd]");
                    $("#Delete").prop("disabled",true);
                    $("#DeleteFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");

                    $("#InspectFrame").addClass("bg-[#dddddd]");
                    $("#Inspect").prop("disabled",true);
                    $("#InspectFrame").removeClass("bg-[#42bee4] hover:bg-[#368fb3] cursor-pointer");
                }
            }
            function ValidToken(array){
                try{
                    let isValid = true;
                    $.each(array,function(k,v){
                        if ($("#"+v).children()[6].textContent.length === 0){
                            isValid = false;
                        }
                    });
                    return isValid;
                }
                catch(e){
                    console.log(e);
                }
            }
            //select box stuff 
            $js('ItemChecked',function(e,id){
                if (e.target.type == "checkbox"){
                    if (e.target.checked == true){
                        ItemsSelected.push(id);
                        $("#"+SpaceToUnderScore(id)).addClass("bg-[#f8c200]");
                        closeEditMenu();
                        EnableDisableEditDelete();
                    }
                    else{
                        let index = ItemsSelected.indexOf(id);
                        $("#"+SpaceToUnderScore(id)).removeClass("bg-[#f8c200]");
                        ItemsSelected.splice(index,1);
                        closeEditMenu();
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
                            let obj = $(CheckBoxes[i].parentNode.parentNode).children()[3].textContent;
                            ItemsSelected.push(obj);
                        }
                        CheckBoxes[i].checked = true
                        $(CheckBoxes[i].parentNode.parentNode).addClass("bg-[#f8c200]");
                    }
                    closeEditMenu();
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
                    EnableDisableEditDelete();
                }
            });
            $js("ChangeComponent",async function(ev,Component){
                ShowLoading();
                await $wire.call("setComponent",Component)
                ShowLoading();
                await $wire.call("LoadRoles");
                await refresh();
                EnableDisableEditDelete();
            })
            $js("ChangeApplication",async function(ev,Application){
                ShowLoading();
                await $wire.call("SetApplication",Application)
                ShowLoading();
                await $wire.call("LoadRoles");
                await refresh();
                EnableDisableEditDelete();
            })
            function PopulateArrayWithVals(EditAdd){
                let FormVals = [];
                FormVals.push($(`#${EditAdd} #nApiToken`).text());
                return FormVals;
            }
            function SpaceToUnderScore(input){
                return input.replaceAll(" ","_");
            }
            //used for editing
            $js("EditConfirm",function(e){
                e.preventDefault();
                let FormVals = PopulateArrayWithVals("EditToken");
                let OGCopy = $("#"+SpaceToUnderScore(EditItem)).clone(false);
                $("#"+SpaceToUnderScore(EditItem)).children().each(function(index){
                    
                    //we exclude the checkbox, sequence num, etc
                    if (index ==6){
                        $(this).text(FormVals[0]);
                    }
                });
                    ActionsDone.push("UPDATE["+EditItem+"]~!~"+JSON.stringify(TRToObject($("#"+SpaceToUnderScore(EditItem)))))
                    console.log(ActionsDone);
                    setTimeout(function(){
                        $("#"+SpaceToUnderScore(EditItem)).children().first().children().click(); //clicks the checkbox
                    },100);
                    //now we close the menu
                    setAlertText("Successfully generated API Token");
                    displayAlert();
                    closeEditMenu();
                
            });
            function closeEditMenu(){
                EditMenuStatus = false;
                $("#EditMenu").addClass("opacity-0");
                setTimeout(function(){
                    $("#EditMenu").addClass("hide");
                },110);       
            }
            function OpenDetailsModal(){
                $("#DisplayMessageFrame").removeClass("opacity-0 ease-in duration-200");
                $("#DisplayMessageFrame").addClass("opacity-100 ease-out duration-300");
                $("#DisplayMessageMain").removeClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
                $("#DisplayMessageMain").addClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95");
            }
            //used to open and close the add menu
            $js("CloseOpenInspect",async function(){
                if (EditMenuStatus == true || DeleteMenuStatus == true){
                    return;
                }
                //fine to use edit item since only one api key can be inspected at a time
                let Obj = TRToObject($("#"+SpaceToUnderScore(EditItem)));
                let Decrypted = await $wire.call("DecryptMessage",Obj["API TOKEN"], Obj["USERNAME"])
                if (Decrypted !== false){
                    let Info = Decrypted.split(",");
                    $("#API_Username").text(Obj["USERNAME"]);
                    $("#API_UserId").text(Info[0]);
                    $("#API_Application_id").text(Info[1]);
                    $("#API_Role_id").text(Info[2]);
                    $("#API_DateCreated").text(Info[3]);
                    $("#DisplayMessage").removeClass("hide")
                    setTimeout(function(e){OpenDetailsModal()},50)
                    $("#"+EditItem).children().first().children().click();
                }
                else{
                    setAlertText("Please save before inspecting token");
                    displayAlert();
                }
            });
            //used to open and close edit menu
            $js("CloseOpenEdit",function(){
                if (DeleteMenuStatus == true){
                    return;
                }
                if (EditMenuStatus == false){
                    EditMenuStatus = true;
                    $("#EditMenu").removeClass("hide");
                    $("#EditMenu").removeClass("opacity-0");
                    let Obj = TRToObject($("#"+SpaceToUnderScore(EditItem)));
                    $("#EditToken #userName").text(Obj["USERNAME"]);
                    $("#EditToken #application").text(Obj["APPLICATION"]);
                    $("#EditToken #role").text(Obj["ROLE"]);
                    $("#EditToken #cApiToken").text(Obj["API TOKEN"]);
                    $("#EditToken #nApiToken").text("Will generate automatically");
                    $("#EditToken #creationTime").text(Obj["CREATION TIME"]);
                    $("#EditToken #expiryDate").text(Obj["EXPIRY DATE"]);
                    $("#EditToken #createdBy").text(Obj["CREATED BY"]);
                    $("#EditToken #description").text(Obj["DESCRIPTION"]);
                }
                else{
                    closeEditMenu();
                }
            });
            $js("OpenDeleteModal",async function(){
                if (EditMenuStatus == true){
                    return;
                }
                let name = $("#"+SpaceToUnderScore(ItemsSelected[0])).children()[6].innerHTML;
                if (ItemsSelected.length == 1){
                    $("#DeleteMessage").text("Are you sure you want to delete the token starting with,")
                    $("#ItemToDelete").text(name.substr(0,8));
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
            $js("refresh",refresh);
            async function refresh(){
                ShowLoading();
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
                        $("#"+SpaceToUnderScore(item)).children()[6].textContent = "";
                    })
                    //NOTE this is not an actual delete but im differentiating it from the update.
                    ActionsDone.push("DELETE~!~"+ItemsToDelete);
                    console.log(ActionsDone);
                    CloseDeleteModal();
                    setTimeout(function(){
                        $("#DeleteModal").addClass("hide");
                    },200);
                    setAlertText("Successfully deleted Api Token(s)");
                    displayAlert();
                });

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
                    "<span class=\"absolute top-[35%] left-[38%]\" wire:loading colspan=\"999\"><img src=\"/images/Loading_2.gif\"></span>"
                )
                $("#InfoTable").text(""); //clearing current Info
                $("#InfoTable").append(LoadingTD);
            }
            $js("saveToDB",async function(ev){
                ShowLoading();
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
                                ErrorMsg += "Failed to update Api Access Token \"" + Obj["TOKEN"] + "\"<br>";
                            }
                        }
                        else if (Type.includes("INSERT")){
                            if (Result[index] != true){
                                Errors = true;
                                let Obj = JSON.parse(ItemInfo);
                                ErrorMsg += "Failed to insert Api Access Token \"" + Obj["TOKEN"] + "\"<br>";
                            }
                        }
                        else if (Type.includes("DELETE")){
                            if (Result[index] == 0){
                                Errors = true;
                                ErrorMsg += "Failed to delete Api Access Token(s) " + ItemInfo + "<br>";
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
            //generate Sequence Numbers on load ------------------------------------------------------------------------ON LOAD SEGMENT---------------------------
            $(document).ready(async function(){
                //this could be improved by simply lowering the amount of wire.calls to reduce network traffic. leaving like this for now.
                await $wire.call("LoadSoftwareComponents");
                await $wire.call("setDefaultComponent");
                await $wire.call("LoadApplications");
                await $wire.call("setDefaultApplication");
                await $wire.call("LoadRoles");
                await refresh();
                EnableDisableEditDelete();
            })
            //-----------------------------------------------------------------------------------------------------------------------------------------------------
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
                Tr.id = obj["DeviceType ID"];
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
                            console.log(value)
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
                    let result = exportToCsv("ApiAccessInfo.csv",TableObjects);
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