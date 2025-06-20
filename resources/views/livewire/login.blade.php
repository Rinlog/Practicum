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
                <div class="bg-[url(/public/images/LoginBackground.jpeg)] bg-no-repeat bg-cover grid grid-cols-12 w-screen h-screen">
                    <div class="col-span-12"></div> <!--centering our login with these divs -->
                    <div class="lg:col-span-7 col-span-4"></div>
                    <div id="LoginSection" class="relative z-1 lg:col-span-3 md:col-span-5 col-span-12 bg-[#fdfdfd] rounded-lg flex jusify-center items-center flex-col shadow-md h-125 lg:h-133" >
                        <img id="ExpandedLogo" src="\images\NBCC_Horizontal_White.png" class="absolute w-[200px] right-0 top-[-50px] z-2">
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
                                <x-bigbutton wireclick="$js.HandleLogin" text="ENTER" id="loginbutton"></x-bigbutton>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="invisible lg:visible bg-[#3081ad]/80 h-screen w-999 absolute left-340 bottom-0">
                </div>
            </main>
            </div>
        </body>
        <livewire:modals.change-password key="{{ Str::random() }}"></livewire:modals.change-password>
    </html>
</div>
@script
<script>
    $js("HandleLogin",async function(){
        let DefaultPass = await $wire.call("CheckForDefaultPass");
        if (DefaultPass == true){

            OpenModal();
            $("#ConfirmChange").click(async function(ev){
                let Password = $("#ChangePassword");
                let ConfirmPass = $("#ChangeConfirmPassword");
                let PasswordErr = $("#ChangePasswordErr");
                let ConfirmPassErr = $("#ChangeConfirmPasswordErr")

                Password.removeClass("border-2 border-red-500");
                PasswordErr.addClass("hide");
                PasswordErr.text("");
                ConfirmPass.removeClass("border-2 border-red-500");
                ConfirmPassErr.addClass("hide");
                ConfirmPassErr.text("");

                if (Password.val() == "" && ConfirmPass.val() == ""){
                    Password.addClass("border-2 border-red-500");
                    PasswordErr.removeClass("hide");
                    PasswordErr.text("Password can not be blank");
                    ConfirmPass.addClass("border-2 border-red-500");
                    ConfirmPassErr.removeClass("hide");
                    ConfirmPassErr.text("Confirm pass can not be blank");
                    return;
                }
                else if (Password.val() == ""){
                    Password.addClass("border-2 border-red-500");
                    PasswordErr.removeClass("hide");
                    PasswordErr.text("Password can not be blank");
                    return;
                }
                else if (ConfirmPass.val() == ""){
                    ConfirmPass.addClass("border-2 border-red-500");
                    ConfirmPassErr.removeClass("hide");
                    ConfirmPassErr.text("Confirm pass can not be blank");
                    return;
                }
                if (Password.val() == ConfirmPass.val()){
                    CloseModal();
                    setTimeout(async function(){
                        let result = await $wire.call("ChangePass",Password.val());
                        if (result == true){
                            await $wire.call("login");
                        }
                    },200)

                }
                else{
                    Password.addClass("border-2 border-red-500");
                    PasswordErr.removeClass("hide");
                    PasswordErr.text("Passwords do not match");
                    ConfirmPass.addClass("border-2 border-red-500");
                    ConfirmPassErr.removeClass("hide");
                    ConfirmPassErr.text("Passwords do not match");
                }
            })
        }
        else{
            await $wire.call("login");
        }
    })
    function OpenModal(){
        $("#ChangePassModal").removeClass("hide");
        setTimeout(function(){
            $("#ChangePassModalFrame").removeClass("opacity-0 ease-in duration-200");
            $("#ChangePassModalFrame").addClass("opacity-100 ease-out duration-300");
            $("#ChangePassModalMain").removeClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
            $("#ChangePassModalMain").addClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95 duration-300");
        },50)
    }
    function CloseModal(){
        $("#ChangePassModalFrame").addClass("opacity-0 ease-in duration-200");
        $("#ChangePassModalFrame").removeClass("opacity-100 ease-out duration-300");
        $("#ChangePassModalMain").addClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
        $("#ChangePassModalMain").removeClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95");
        setTimeout(() => {
            $("#ChangePassModal").addClass("hide");
        }, 200);
    }
</script>
@endscript


