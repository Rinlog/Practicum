<div class="bg-[#f2f2f2] flex flex-row overflow-x-hidden block">
    @if (empty($_SESSION["UserName"]))
        <script>window.location = "/";</script>
    @endif
    <livewire:navigation></livewire:navigation>
    {{-- main section --}}
    <div class="w-screen flex flex-col flex-wrap">
        <livewire:usercontrols.usercontrolnav></livewire:usercontrols.usercontrolnav>
        <div class="lg:pb-10 lg:pl-10 md:pb-10 md:pl-10 pb-15 pt-4 lg:min-h-[857px] min-h-[1000px] w-full overflow-y-hidden">
            <h1 class="text-[#7e7e7e] pb-5">Profile Settings</h1>
            {{-- general settings --}}
            <div class="bg-white sm:p-10 lg:w-[60%] rounded-lg shadow-md sm:grid sm:grid-cols-2 min-h-[600px] gap-8">
                <div class="col-1 flex flex-col gap-4">
                    <label class="cursor-text text-[#146b8e] text-4xl font-semibold w-fit">General</label>
                    <h3 class="text-[#146b8e] font-semibold flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" class="fill-[#00719d] min-h-[24px] min-w-[24px]" width="24px" height="24px">
                            <path id="Profile" class="cls-1" d="M13,13c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5ZM13,5c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3ZM20.5,22v-2c0-2.76-2.24-5-5-5h-5c-2.76,0-5,2.24-5,5v2c0,.55.45,1,1,1s1-.45,1-1v-2c0-1.65,1.35-3,3-3h5c1.65,0,3,1.35,3,3v2c0,.55.45,1,1,1s1-.45,1-1Z"/>
                        </svg>
                        PERSONAL DETAILS
                    </h3>
                    <div class="flex flex-col gap-4 pb-10">
                        <span class="w-full">
                            <label class="text-[#146b8e]">First name</label>
                            <x-input src="FName" id="FName" placeholder="Firstname"></x-input>
                            <label id="FNameErr" class="hide text-red-500"></label>
                        </span>
                        <span class="w-full">
                            <label class="text-[#146b8e]">Last name</label>
                            <x-input  src="LName" id="LName" placeholder="Lastname"></x-input>
                            <label id="LNameErr" class="hide text-red-500"></label>
                        </span>
                    </div>
                    <h3 class="text-[#146b8e] font-semibold flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" class="fill-[#00719d] min-h-[24px] min-w-[24px]" width="24px" height="24px">
                            <path id="Contact" class="cls-1" d="M21.97,16.4l-4-1.92c-.88-.43-1.94-.28-2.67.37l-1.33,1.31h0c-.59-.38-1.46-1.01-2.22-1.82-.63-.68-1.1-1.37-1.43-1.92-.15-.25-.1-.57.11-.77l.87-.85c.77-.75.96-1.92.46-2.88l-2.03-3.92c-.32-.63-.97-1.01-1.68-.98-.79.04-2.31.26-3.55,1.38-1.58,1.42-1.93,3.78-1,6.66.89,2.76,2.42,5.42,4.18,7.3,2.78,2.96,6.32,4.66,9.71,4.67h0c4.04,0,5.23-3.19,5.55-4.57.19-.81-.22-1.67-.98-2.04ZM17.4,21.18h0c-2.89,0-5.95-1.5-8.39-4.1-1.56-1.66-2.97-4.13-3.77-6.61-.4-1.25-.88-3.52.49-4.74.64-.57,1.41-.79,1.98-.87.26-.04.51.1.63.34l1.83,3.55c.13.25.08.55-.12.74l-1.22,1.19c-.58.57-.72,1.43-.34,2.15.36.69,1,1.73,1.95,2.75.89.95,1.89,1.67,2.57,2.11.68.43,1.57.37,2.17-.17l1.33-1.31c.19-.17.46-.2.69-.1l3.52,1.68c.29.14.43.47.33.77-.44,1.3-1.43,2.63-3.64,2.63Z"/>
                        </svg>
                        CONTACT
                    </h3>
                    <div class="flex flex-col gap-4">
                        <span class="w-full">
                            <label class="text-[#146b8e]">Email</label>
                            <x-input  src="Email" id="Email" placeholder="Email"></x-input>
                            <label id="EmailErr" class="hide text-red-500"></label>
                        </span>
                        <span class="w-full">
                            <label class="text-[#146b8e]">Phone</label>
                            <x-input customStyle="" src="Phone" id="Phone" placeholder="Phone"></x-input>
                            <label id="PhoneErr" class="hide text-red-500"></label>
                        </span>
                    </div>
                    <span class="flex sm:justify-end">
                        <button wire:click="$js.saveToDBGeneral" id="save" class=" sm:w-[40%] save flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                            <svg class="svg" stroke="#4fbce7" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <polyline data-name="Right" fill="none" id="Right-2" points="7.9 12.3 12 16.3 16.1 12.3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="2.7" y2="14.2"/>
                            </svg>
                            SAVE CHANGES
                        </button>
                    </span>
                </div>
                <div class="col-2 bg-[#146b8e] rounded-lg shadow-md p-10">
                        <label class="text-white text-3xl font-semibold">Account Information</label>
                        <div class="flex flex-col gap-8 py-5">
                            <span class="w-full">
                                <label class="text-white">Username</label>
                                <x-disabled-input src="Username" id="Username" type="text" placeholder="Username*" customStyle="bg-[#00638a] text-white"></</x-disabled-input>
                            </span>
                            <span class="w-full">
                                <label class="text-white">Organization</label>
                                <x-disabled-input src="Organization" id="Username" type="text" placeholder="Organization*" customStyle="bg-[#00638a] text-white"></x-disabled-input>
                            </span>
                            <span class="w-full">
                                <label class="text-white">Applications</label>
                                <x-disabled-input src="Application" id="Username" type="text" placeholder="Applications*" customStyle="bg-[#00638a] text-white"></x-disabled-input>
                            </span>
                            <span class="w-full">
                                <label class="text-white">Roles</label>
                                <x-disabled-input src="Role" id="Username" type="text" placeholder="Roles*" customStyle="bg-[#00638a] text-white"></x-disabled-input>
                            </span>
                        </div>
                        <span class="pr-7 sm:pr-0">
                            <label class="text-white">*Read-only. Contact the system administrator for edits.</label>
                        </span>
                </div>
            </div>
            {{-- Security settings --}}
            <div class="bg-white sm:p-10 lg:w-[60%] rounded-lg shadow-md flex flex-col min-h-[400px] gap-8 mt-10">
                    <label class="cursor-text w-fit text-[#146b8e] text-4xl font-semibold">Security Settings</label>
                    <h3 class="text-[#146b8e] font-semibold flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" class="fill-[#00719d] min-h-[24px] min-w-[24px]" width="24px" height="24px">
                        <path id="Password" class="cls-1" d="M20.13,9h-.13v-1.03c0-3.84-3.14-6.97-7-6.97s-7,3.13-7,6.97v1.03h-.13c-2.13,0-3.87,1.74-3.87,3.87v8.26c0,2.13,1.74,3.87,3.87,3.87h14.26c2.13,0,3.87-1.74,3.87-3.87v-8.26c0-2.13-1.74-3.87-3.87-3.87ZM8,7.97c0-2.74,2.24-4.97,5-4.97s5,2.22,5,4.97v1.03H8v-1.03ZM22,21.13c0,1.03-.84,1.87-1.87,1.87H5.87c-1.03,0-1.87-.84-1.87-1.87v-8.26c0-1.03.84-1.87,1.87-1.87h14.26c1.03,0,1.87.84,1.87,1.87v8.26ZM15,16c0,.74-.4,1.38-1,1.72v1.28c0,.55-.45,1-1,1s-1-.45-1-1v-1.28c-.6-.35-1-.98-1-1.72,0-1.1.9-2,2-2s2,.9,2,2Z"/>
                        </svg>
                        CHANGE PASSWORD
                    </h3>
                    <div class="flex flex-col gap-4 w-[50%]">
                        <span class="relative w-full">
                            <x-input src="CurrentPass" type="password" id="CurrentPass" placeholder="Current-Password"></x-input>
                            <svg wire:click="$js.hideShow('show','CurrentPass','CurrentPassShow','CurrentPassHide')" id="CurrentPassShow" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class=" absolute top-3 right-3 cursor-pointer min-h-[24px] min-h-[24px]">
                                <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg wire:click="$js.hideShow('hide','CurrentPass','CurrentPassHide','CurrentPassShow')" id="CurrentPassHide" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class="hide absolute top-3 right-3 cursor-pointer min-h-[24px] min-h-[24px]">
                                <path d="M9.60997 9.60714C8.05503 10.4549 7 12.1043 7 14C7 16.7614 9.23858 19 12 19C13.8966 19 15.5466 17.944 16.3941 16.3878M21 14C21 9.02944 16.9706 5 12 5C11.5582 5 11.1238 5.03184 10.699 5.09334M3 14C3 11.0069 4.46104 8.35513 6.70883 6.71886M3 3L21 21" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <label id="CurrentPassErr" class="hide text-red-500"></label>
                        </span>
                        <span class="relative w-full">
                            <x-input customStyle="" type="password" src="NewPass" id="NewPass" placeholder="New-Password"></x-input>
                            <svg wire:click="$js.hideShow('show','NewPass','NewPassShow','NewPassHide')" id="NewPassShow" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class="absolute top-3 right-3 cursor-pointer min-h-[24px] min-h-[24px]">
                                <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg wire:click="$js.hideShow('hide','NewPass','NewPassHide','NewPassShow')" id="NewPassHide" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class="hide absolute top-3 right-3 cursor-pointer  min-h-[24px] min-h-[24px]">
                                <path d="M9.60997 9.60714C8.05503 10.4549 7 12.1043 7 14C7 16.7614 9.23858 19 12 19C13.8966 19 15.5466 17.944 16.3941 16.3878M21 14C21 9.02944 16.9706 5 12 5C11.5582 5 11.1238 5.03184 10.699 5.09334M3 14C3 11.0069 4.46104 8.35513 6.70883 6.71886M3 3L21 21" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <label id="NewPassErr" class="hide text-red-500"></label>
                        </span>
                        <span class="relative w-full">
                            <x-input customStyle="" type="password" src="ConfirmPass" id="ConfirmPass" placeholder="Confirm-Password"></x-input>
                            <svg wire:click="$js.hideShow('show','ConfirmPass','ConfirmPassShow','ConfirmPassHide')" id="ConfirmPassShow" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class="absolute top-3 right-3 cursor-pointer min-h-[24px] min-h-[24px]">
                                <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg wire:click="$js.hideShow('hide','ConfirmPass','ConfirmPassHide','ConfirmPassShow')" id="ConfirmPassHide" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" class="hide absolute top-3 right-3 cursor-pointer  min-h-[24px] min-h-[24px]">
                                <path d="M9.60997 9.60714C8.05503 10.4549 7 12.1043 7 14C7 16.7614 9.23858 19 12 19C13.8966 19 15.5466 17.944 16.3941 16.3878M21 14C21 9.02944 16.9706 5 12 5C11.5582 5 11.1238 5.03184 10.699 5.09334M3 14C3 11.0069 4.46104 8.35513 6.70883 6.71886M3 3L21 21" stroke="#00719d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <label id="ConfirmPassErr" class="hide text-red-500"></label>
                        </span>
                    </div>
                    <span class="flex w-[50%] justify-end">
                        <button wire:click="$js.saveToDBSecurity" id="save" class=" sm:w-[40%] save flex text-[#4fbce7] font-semibold gap-3 border-2 rounded-full p-3 pl-5 pr-5 items-center justify-center hover:text-[#3c8fb0] cursor-pointer">
                            <svg class="svg" stroke="#4fbce7" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <polyline data-name="Right" fill="none" id="Right-2" points="7.9 12.3 12 16.3 16.1 12.3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="2.7" y2="14.2"/>
                            </svg>
                            SAVE CHANGES
                        </button>
                    </span>
            </div>
            <livewire:alert.notification></livewire:alert.notification>
        </div>
    </div>
</div>

@script
<script>
if (0){}

    $js("saveToDBGeneral",async function(){
        let Fname = $("#FName").val();
        let Lname = $("#LName").val();
        let Email = $("#Email").val();
        let Phone = $("#Phone").val();
        let Isvalid = true;
        let EmailRegex = /(?:[a-z0-9!#$%&'*+\x2f=?^_`\x7b-\x7d~\x2d]+(?:\.[a-z0-9!#$%&'*+\x2f=?^_`\x7b-\x7d~\x2d]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9\x2d]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9\x2d]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9\x2d]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
        let PhoneRegex = /^(\+\d{1,2}\s?)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/;
        if (Fname == ""){
            Isvalid = false;
            ShowFNameErr("First name cannot be empty");
        }
        else{
            HideFNameErr();
        }
        if (Lname == ""){
            Isvalid = false;
            ShowLNameErr("Last name cannot be empty")
        }
        else{
            HideLNameErr();
        }
        if (EmailRegex.test(Email) == false && Email != ""){
            Isvalid = false;
            ShowEmailErr("Invalid email");
        }
        else{
            HideEmailErr();
        }
        if (PhoneRegex.test(Phone) == false && Phone != ""){
            Isvalid = false;
            ShowPhoneErr("Invalid phone number");
        }
        else{
            HidePhoneErr();
        }
        if (Isvalid == true){
            let Result = await $wire.call("SaveGeneralInfo");
            
            if (Result == 1){
                setAlertText("Successfully updated info");
                displayAlert();
            }
            else{
                setAlertText("Failed to update info");
                displayAlert();
            }
        }
    })
    $js("saveToDBSecurity",async function(){
        try{
            let CurrentPass = $("#CurrentPass").val();
            let NewPass = $("#NewPass").val();
            let ConfirmPass = $("#ConfirmPass").val();
            let Isvalid = true;
            if (CurrentPass == ""){
                Isvalid = false;
                ShowCurrentPassErr("Current password cannot be empty");
            }
            else{
                HideCurrentPassErr();
            }
            if (NewPass == ""){
                Isvalid = false;
                ShowNewPassErr("New password cannot be empty");
            }
            else{
                HideNewPassErr();
            }
            if (ConfirmPass == ""){
                Isvalid = false;
                ShowConfirmPassErr("Confirm password cannot be empty");
            }
            else if (NewPass != ConfirmPass){
                Isvalid = false;
                ShowConfirmPassErr("Passwords do not match");
            }
            else{
                HideConfirmPassErr();
            }
            if (Isvalid == true){
                let result = await $wire.call("VerifyCurrentPassword");
                if (result == true){
                    HideCurrentPassErr();
                    $wire.call("ChangePass");
                    window.scrollTo(0,0);
                    setAlertText("Password changed successfully");
                    displayAlert();
                    $("#CurrentPass").text("");
                    $("#NewPass").text("");
                    $("#ConfirmPass").text("");
                }
                else{
                    ShowCurrentPassErr("Current password incorrect");
                }
            }
        }
        catch(e){

        }
    })
    $js("hideShow",function(ShowHide,id,svgToHide,svgToShow){
        if (ShowHide == "show"){
            $("#"+id).attr("type","text");
            $("#"+svgToHide).addClass("hide");
            $("#"+svgToShow).removeClass("hide");
        }
        else{
            $("#"+id).attr("type","password");
            $("#"+svgToHide).addClass("hide");
            $("#"+svgToShow).removeClass("hide");
        }
    })
    function ShowFNameErr(msg){
        let FName = $("#FName");
        let FNameErr = $("#FNameErr");
        FName.addClass("border-2 border-red-500");
        FNameErr.removeClass("hide");
        FNameErr.text(msg);
    }
    function HideFNameErr(){
        let FName = $("#FName");
        let FNameErr = $("#FNameErr");
        FName.removeClass("border-2 border-red-500");
        FNameErr.addClass("hide");
        FNameErr.text("");
    }
    function ShowLNameErr(msg){
        let LName = $("#LName");
        let LNameErr = $("#LNameErr");
        LName.addClass("border-2 border-red-500");
        LNameErr.removeClass("hide");
        LNameErr.text(msg);
    }
    function HideLNameErr(){
        let LName = $("#LName");
        let LNameErr = $("#LNameErr");
        LName.removeClass("border-2 border-red-500");
        LNameErr.addClass("hide");
        LNameErr.text("");
    }
    function ShowEmailErr(msg){
        let Email = $("#Email");
        let EmailErr = $("#EmailErr");
        Email.addClass("border-2 border-red-500");
        EmailErr.removeClass("hide");
        EmailErr.text(msg);
    }
    function HideEmailErr(){
        let Email = $("#Email");
        let EmailErr = $("#EmailErr");
        Email.removeClass("border-2 border-red-500");
        EmailErr.addClass("hide");
        EmailErr.text("");
    }
    function ShowPhoneErr(msg){
        let Phone = $("#Phone");
        let PhoneErr = $("#PhoneErr");
        Phone.addClass("border-2 border-red-500");
        PhoneErr.removeClass("hide");
        PhoneErr.text(msg);
    }
    function HidePhoneErr(){
        let Phone = $("#Phone");
        let PhoneErr = $("#PhoneErr");
        Phone.removeClass("border-2 border-red-500");
        PhoneErr.addClass("hide");
        PhoneErr.text("");
    }
    function ShowCurrentPassErr(msg){
        let CurrentPass = $("#CurrentPass");
        let CurrentPassErr = $("#CurrentPassErr");
        CurrentPass.addClass("border-2 border-red-500");
        CurrentPassErr.removeClass("hide");
        CurrentPassErr.text(msg);
    }
    function HideCurrentPassErr(){
        let CurrentPass = $("#CurrentPass");
        let CurrentPassErr = $("#CurrentPassErr");
        CurrentPass.removeClass("border-2 border-red-500");
        CurrentPassErr.addClass("hide");
        CurrentPassErr.text("");
    }
    function ShowNewPassErr(msg){
        let NewPass = $("#NewPass");
        let NewPassErr = $("#NewPassErr");
        NewPass.addClass("border-2 border-red-500");
        NewPassErr.removeClass("hide");
        NewPassErr.text(msg);
    }
    function HideNewPassErr(){
        let NewPass = $("#NewPass");
        let NewPassErr = $("#NewPassErr");
        NewPass.removeClass("border-2 border-red-500");
        NewPassErr.addClass("hide");
        NewPassErr.text("");
    }
    function ShowConfirmPassErr(msg){
        let ConfirmPass = $("#ConfirmPass");
        let ConfirmPassErr = $("#ConfirmPassErr");
        ConfirmPass.addClass("border-2 border-red-500");
        ConfirmPassErr.removeClass("hide");
        ConfirmPassErr.text(msg);
    }
    function HideConfirmPassErr(){
        let ConfirmPass = $("#ConfirmPass");
        let ConfirmPassErr = $("#ConfirmPassErr");
        ConfirmPass.removeClass("border-2 border-red-500");
        ConfirmPassErr.addClass("hide");
        ConfirmPassErr.text("");
    }
</script>
@endscript