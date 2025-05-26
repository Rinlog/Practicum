<?php session_start(); ?>
<div>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
            @vite(['resources/scss/login.scss'])
            {{--@vite(['resources\js\ComponentJS\alertJS.js'])--}}
            @if (!empty($_SESSION["UserName"]))
                <script>window.location = "/home";</script>
            @endif
        </head>
        <body class="bg-[#FDFDFC] text-[#1b1b18] flex items-center items-center min-h-screen flex-col">
            {{--
            commenting for later reference
            @if ($open == "show")
                <livewire:modal-alert key="{{ Str::random() }}" :open="$open" :message="$message" :ButtonText="$ButtonText" :title="$title"></livewire:modal-alert>
            @endif
            --}}
            <div>
            <main class="relative">
                <img id="ExpandedLogo" src="\images\NBCC_Horizontal_White.png" class="absolute w-[200px] left-345 top-42 z-2">
                <div class="bg-[url(/public/images/LoginBackground.jpeg)] bg-no-repeat bg-cover grid grid-cols-12 w-screen h-screen">
                    <div class="col-span-12"></div> <!--centering our login with these divs -->
                    <div class="lg:col-span-7 col-span-4"></div>
                    <div id="LoginSection" class="z-1 lg:col-span-3 md:col-span-5 col-span-12 bg-[#fdfdfd] rounded-lg flex jusify-center items-center flex-col shadow-md h-125 lg:h-133" >
                        <span class="flex flex-col justify-center items-center w-full shadow-md bg-neutral-100 rounded-lg">
                            <h1 class="font-bold mt-8 text-[#00719d]">WELCOME</h1>
                            <h3 class="font-bold text-[#00719d]">TO THE INTEGRATED DATA LAYER (IDL)</h3>
                            <hr class="mt-10"/>
                        </span>
                        <h2 class="font-bold text-[#46c0e5] mb-5 mt-13">LOGIN</h2>
                        @if ($usrShowErr == "show")
                        <div class="flex flex-col justify-center w-4/6">
                        @else
                        <div class="flex flex-col gap-6 justify-center w-4/6">
                        @endif
                            <span>
                                <x-input customStyle="{{ $usrCustomStyle }}" src="Username" id="Username" placeholder="Username"></x-input>
                                <label class="{{ $usrShowErr }} text-red-500">{{ $usrErrMsg }}</label>
                            </span>

                            <span>
                                <x-input customStyle="{{ $passCustomStyle }}" type="Password" src="Password" id="Password" placeholder="Password"></x-input>
                                <label class="{{ $passShowErr }} text-red-500">{{ $passErrMsg }}</label>
                            </span>
                            <span class="flex flex-col justify-center items-center w-full mt-3 mb-10">
                                <x-bigbutton wireclick="login" text="ENTER" id="loginbutton"></x-bigbutton>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="invisible lg:visible bg-[#3081ad]/80 h-screen w-999 absolute left-340 bottom-0">
                </div>
            </main>
            </div>
        </body>
    </html>
</div>


