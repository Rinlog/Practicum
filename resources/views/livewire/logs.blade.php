<?php session_start()?>
<div class="bg-[#f2f2f2] flex flex-row">
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div id="InfoSection" class="flex flex-col lg:flex-row lg:w-[1750px] gap-0 lg:overflow-y-hidden">
            <div class="relative w-[90%] md:w-[80%] lg:w-[100%] pl-10 pr-10 pt-5 overflow-y-hidden">
                {{-- info selection --}}
                <div class="flex">
                    <div class="relative inline-block text-left w-full pr-4 lg:pr-0 md:pr-0 bg-[#f6f6f6] rounded-t-lg">
                        <span class="flex flex-row flex-grow">
                            <ul class="flex h-full">
                                @if ($LogPage == "generalLog")
                                    <li wire:click="$js.SwitchToGeneralLog" class="bg-white h-full w-full p-9 pr-12 rounded-t-lg whitespace-nowrap text-[#056c8b] font-bold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToGeneralLog">General Log</h2></li>
                                    <li wire:click="$js.SwitchToApplicationLog" class="bg-[#f6f6f6] h-full w-full p-9 rounded-t-lg whitespace-nowrap text-[#707070] font-semibold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToApplicationLog">Application Log</h2></li>
                                @else
                                    <li wire:click="$js.SwitchToGeneralLog" class="bg-[#f6f6f6] h-full w-full p-9 rounded-t-lg whitespace-nowrap text-[#707070] font-semibold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToGeneralLog">General Log</h2></li>
                                    <li wire:click="$js.SwitchToApplicationLog" class="bg-white h-full w-full p-9 pl-12 rounded-t-lg whitespace-nowrap text-[#056c8b] font-bold hover:bg-neutral-200 cursor-pointer"><h2 wire:click="$js.SwitchToApplicationLog">Application Log</h2></li>
                                @endif
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @if ($LogPage == "generalLog")
            <livewire:logs.general-log></livewire:logs.general-log>
        @elseif ($LogPage == "applicationLog")
            <livewire:logs.application-log></livewire:logs.application-log>
        @endif
    </div>
</div>
@script
<script>
    if (0){}

    $js("SwitchToGeneralLog",function(){
        $wire.set("LogPage","generalLog")
    })
    $js("SwitchToApplicationLog",function(){
        $wire.set("LogPage","applicationLog")
    })

</script>
@endscript