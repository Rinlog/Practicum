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
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div class="p-10 h-screen lg:h-[890px]">
            <div class="bg-[#00719d] w-[45%] rounded-lg p-10 flex flex-col gap-4">
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
        </div>
    </div>
</div>