{{-- user controls section --}}
<?php
include_once(app_path() . "/Includes/OutPutIfLoggedIn.php");
?>
<div id="UserControlMainDiv" class="relative w-screen flex justify-end items-center bg-white h-15 shadow-md">
    @vite('resources/js/ComponentJS/usercontrolnav.js')
    @vite('app/Includes/OutPutIfLoggedIn.php")?>')
    <div>
        <div class="inline-block text-left">
            <div id="UserControlDiv" class="transition-all duration-300 pr-25">
                <button type="button" id="OpenUserControls" class="inline-flex w-full justify-center items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" id="menu-button" aria-expanded="true" aria-haspopup="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#767676" id="Path" viewBox="0 0 26 26" width="24px" height="24px">
                    <path id="Profile" class="cls-1" d="M13,13c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5ZM13,5c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3ZM20.5,22v-2c0-2.76-2.24-5-5-5h-5c-2.76,0-5,2.24-5,5v2c0,.55.45,1,1,1s1-.45,1-1v-2c0-1.65,1.35-3,3-3h5c1.65,0,3,1.35,3,3v2c0,.55.45,1,1,1s1-.45,1-1Z"/>
                </svg>
                <label class="text-[#767676]"><?php OutPutIfLoggedIn("UserName");?></label>
                <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
                </button>
            </div>

            <div id="UserDropDown" isOpen="false" class="absolute right-25 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden transform opacity-0 scale-0" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                <div class="py-1" role="none">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 hover:outline-hidden" role="menuitem" tabindex="-1" id="menu-item-0">Edit Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 hover:outline-hidden" role="menuitem" tabindex="-1" id="menu-item-0">Change Password</a>
                <form method="post" action="/logout" role="none">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 hover:outline-hidden cursor-pointer" role="menuitem" tabindex="-1" id="menu-item-3">Sign out</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
