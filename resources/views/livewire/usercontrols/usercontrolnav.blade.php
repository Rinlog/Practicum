{{-- user controls section --}}
<?php
include_once(app_path() . "/Includes/OutPutIfLoggedIn.php");
?>
<div id="UserControlMainDiv" class="relative w-screen flex justify-end items-center bg-white h-15 shadow-md">
    @vite('resources/js/ComponentJS/usercontrolnav.js')
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
                <a href="#" class="flex gap-2 px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 hover:outline-hidden" role="menuitem" tabindex="-1" id="menu-item-0">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" class="fill-[#707070] min-h-[24px] min-w-[24px]" width="24px" height="24px">
                        <path id="Settings" class="cls-1" d="M13,3.83c.14,0,.28.02.42.05.64.15,1.14.65,1.29,1.29.19.8.91,1.34,1.71,1.34.14,0,.28-.02.42-.05.18-.04.35-.11.5-.21.29-.17.6-.26.91-.26.59,0,1.17.3,1.5.84.34.56.34,1.27,0,1.83-.5.83-.24,1.91.59,2.41.15.09.32.16.5.21.94.23,1.52,1.18,1.29,2.12-.15.64-.65,1.14-1.29,1.29-.94.23-1.52,1.18-1.29,2.12.04.18.11.34.21.5.5.83.24,1.91-.59,2.41-.28.17-.6.26-.91.26s-.63-.09-.91-.26c-.29-.17-.6-.26-.91-.26-.59,0-1.17.3-1.5.84-.09.15-.16.32-.21.5-.19.8-.91,1.34-1.7,1.34-.14,0-.28-.02-.42-.05-.64-.15-1.14-.65-1.29-1.29-.2-.8-.91-1.34-1.71-1.34-.14,0-.28.02-.42.05-.18.04-.34.11-.5.21-.29.17-.6.26-.91.26-.59,0-1.17-.3-1.5-.84-.34-.56-.34-1.27,0-1.83.5-.83.24-1.91-.59-2.41-.15-.09-.32-.16-.5-.21-.94-.23-1.52-1.18-1.29-2.12.15-.64.65-1.14,1.29-1.29.94-.23,1.52-1.18,1.29-2.12-.04-.18-.11-.34-.21-.5-.5-.83-.24-1.91.59-2.41.28-.17.6-.26.91-.26s.63.09.91.26c.29.17.6.26.91.26.59,0,1.17-.3,1.5-.84.09-.15.16-.32.21-.5h0c.19-.8.91-1.34,1.7-1.34M13,2h0c-1.63,0-3.04,1.09-3.46,2.65-.51-.31-1.14-.48-1.77-.48s-1.3.18-1.87.52c-.82.5-1.39,1.29-1.62,2.22-.22.9-.09,1.83.37,2.63-1.26.34-2.23,1.34-2.54,2.62-.23.93-.08,1.9.42,2.71.48.79,1.23,1.35,2.12,1.59-.65,1.14-.63,2.52.05,3.64.66,1.08,1.8,1.72,3.07,1.72.62,0,1.24-.16,1.78-.47.34,1.26,1.34,2.23,2.62,2.54.28.07.56.1.85.1,1.63,0,3.04-1.09,3.46-2.65.51.31,1.14.48,1.78.48s1.3-.18,1.87-.52c.82-.5,1.39-1.29,1.62-2.22.22-.9.09-1.83-.37-2.63,1.26-.34,2.23-1.34,2.54-2.62.23-.93.08-1.9-.42-2.71-.48-.79-1.23-1.35-2.12-1.59.65-1.14.63-2.52-.05-3.64-.66-1.08-1.8-1.72-3.07-1.72-.62,0-1.24.16-1.78.47-.34-1.26-1.34-2.23-2.62-2.54-.28-.07-.56-.1-.85-.1h0ZM13,10.25c1.52,0,2.75,1.23,2.75,2.75s-1.23,2.75-2.75,2.75-2.75-1.23-2.75-2.75,1.23-2.75,2.75-2.75M13,8.42c-2.53,0-4.58,2.06-4.58,4.58s2.06,4.58,4.58,4.58,4.58-2.06,4.58-4.58-2.06-4.58-4.58-4.58h0Z"/>
                    </svg>
                    <label class="text-[#767676] cursor-pointer">Edit Profile</label>
                </a>
                <form method="post" action="/logout" role="none">
                    @csrf
                    <button type="submit" class="flex gap-2 w-full  px-4 py-2 text-left text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 hover:outline-hidden cursor-pointer" role="menuitem" tabindex="-1" id="menu-item-3">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" width="24px" height="24px" class="fill-[#707070] min-h-[24px] min-w-[24px]">
                            <path id="Logout" class="cls-1" d="M20,20v1c0,1.65-1.44,3-3.2,3H6.2c-1.76,0-3.2-1.35-3.2-3V5c0-1.65,1.44-3,3.2-3h10.6c1.76,0,3.2,1.35,3.2,3v1c0,.55-.45,1-1,1s-1-.45-1-1v-1c0-.54-.55-1-1.2-1H6.2c-.66,0-1.2.45-1.2,1v16c0,.54.55,1,1.2,1h10.6c.66,0,1.2-.45,1.2-1v-1c0-.55.45-1,1-1s1,.45,1,1ZM22.92,13.38c.1-.24.1-.52,0-.76-.05-.12-.12-.23-.22-.33l-4-4c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2.29,2.29h-5.59c-.55,0-1,.45-1,1s.45,1,1,1h5.59l-2.29,2.29c-.39.39-.39,1.02,0,1.41.2.2.45.29.71.29s.51-.1.71-.29l4-4c.09-.09.17-.2.22-.33Z"/>
                        </svg>
                        <label class="text-[#767676] cursor-pointer">Logout</label>
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
