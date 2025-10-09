<?php
include_once(app_path() . "/Includes/OutPutIfLoggedIn.php");
?>
<div class="bg-[#f2f2f2] flex flex-row">
    @vite('resources/css/home.css')
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap max-h-[890px]">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div class="p-10 h-screen lg:min-h-[857px] grid grid-cols-2 grid-rows-5 sm:gap-x-10 gap-x-20">
            <div class="bg-[#00719d] shadow-md w-full rounded-lg p-10 flex flex-col gap-4 col-1">
                <h1 class="text-white">Hello, <b><?php OutPutIfLoggedIn("UserName");?>!</b></h1>
                <span class="text-[#48bbe7] text-lg font-semibold">
                    @foreach( $userRoles as $role)
                        @if ($loop->index == 0)
                            {{ strtoupper($role->role_name) }}
                        @else
                            {{", " . strtoupper($role->role_name) }}
                        @endif
                    @endforeach
                </span>
            </div>
            <div class="flex pt-10 col-1">
                    <div class="relative inline-block text-left w-full pr-4 lg:pr-0 md:pr-0">
                        <div id="OrganizationSelector" class="w-full flex items-center">
                            <label class="open-sans-soft-regular border-l-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] rounded-l-lg text-white text-lg block p-6 pl-10 h-full shadow-md">Application</label>
                            <div class="selectWrapperLG w-full">
                            <select id="Applications" class="open-sans-soft-regular border-r-1 border-t-1 border-b-1 border-gray-300 border-solid bg-[#707070] w-full text-white text-lg hover:bg-[#4a4a4a] p-6 pr-10 rounded-r-lg font-bold shadow-md" wire:change="SetApplication($event.target.value)">
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
            <div class="col-1 row-span-2">
                <h1 class="text-gray-500">Overview</h1>

                <div class="flex gap-5 py-5 h-full">
                    <div id="RecentLogs" class="bg-white sm:p-10 w-[50%] rounded-lg shadow-md flex flex-col items-start max-h-[140%] min-h-[140%]">
                        <h3 class="text-[#00719d] font-semibold">RECENT LOGS</h3>
                        <table class="rounded-lg  border-separate w-full block overflow-y-auto overflow-x-auto border-spacing-[0]">
                            <tbody class="align-top">
                                {!! $DisplayLogTableInfo !!}
                            </tbody>
                        </table>
                        <span class="p-2 flex w-full justify-end items-end">
                            <h4 class="text-[#74cbdf] font-semibold"><a href="/logs">VIEW ALL &#10148;</a></h4>
                        </span>
                    </div>
                    <div id="Cube2" class="bg-white p-10 w-[50%] rounded-lg shadow-md max-h-[140%] min-h-[140%]">

                    </div>
                </div>
            </div>
            <div class="col-2 row-span-full bg-white p-10 w-[90%] rounded-lg shadow-md">

            </div>
        </div>
    </div>
</div>

@script
<script>

$(document).ready(function(e){
    $wire.call("LoadLogInfo");
})

</script>
@endscript