<?php session_start()?>
<div class="bg-[#f2f2f2] flex flex-row">
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>

        @if ($LogPage == "generalLog")
            <livewire:logs.general-log></livewire:logs.general-log>
        @elseif ($LogPage == "applicationLog")
            <livewire:logs.application-log></livewire:logs.application-log>
        @endif
    </div>
</div>