<?php session_start()?>
<div class="bg-[#f2f2f2] flex flex-row">
    @if (empty(session()->get("AllAppPermsForUser"))))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div id="InfoSection" class="flex flex-col lg:flex-row lg:w-[1750px] gap-0 lg:overflow-y-hidden ">
            <div class="relative w-full lg:flex-1 pl-10 pr-10 pt-5 overflow-y-hidden overflow-x-hidden">
                {{-- info selection --}}
                <div class="flex">
                    <div class="relative inline-block text-left w-full pr-4 lg:pr-0 bg-[#f6f6f6] rounded-t-lg">
                        <span class="flex flex-row flex-grow">
                            <ul class="flex h-full">
                                        @php
                                            // Define the available tabs dynamically based on session permissions
                                            $tabs = [];

                                            if (session('logs-general log')) {
                                                $tabs['generalLog'] = [
                                                    'label' => 'General Log',
                                                    'click' => 'ShowGeneralLog',
                                                ];
                                            }

                                            if (session('logs-application-specific log')) {
                                                $tabs['applicationLog'] = [
                                                    'label' => 'Application Log',
                                                    'click' => 'ShowAppLog',
                                                ];
                                            }
                                        @endphp

                                        @if (!empty($tabs))
                                            @foreach ($tabs as $key => $tab)
                                                @php
                                                    $isActive = $LogPage === $key;
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
        $wire.call("ShowGeneralLog")
    })
    $js("SwitchToApplicationLog",function(){
        $wire.call("ShowAppLog")
    })

</script>
@endscript