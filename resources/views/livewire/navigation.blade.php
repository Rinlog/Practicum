<nav id="SideNav" class="relative w-16 transition-all duration-300 ease-in-out">
        @vite(['resources/css/navigation.css','resources/js/ComponentJS/navigation.js'])
        {{-- left side navbar --}}
        <div id="SideNavFrame" class="w-16 bg-gradient-to-b from-[#00709e] to-[#003a50] transition-all duration-300 ease-in-out text-white lg:min-h-[955px] h-full">
            <div class="shadow-md bg-[#056e99] flex flex-row items-center justify-start h-15">
                <img id="ExpandedLogo" src="\images\NBCC_Horizontal_White.png" class="pl-4 w-[200px] min-w-[220px]">
            </div>
            <ul class="flex flex-col gap-4 lg:justify-center h-[800px]">

                <li id="home" class="slider hover:border-l-4 border-[#49b5d6] cursor-pointer"><a href="/home">
                    <span class="flex flex-row items-center gap-6 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" fill="#FFFFFF" width="24px" height="24px" class="min-w-[24px] min-h-[24px]">
                            <path id="Home" class="cls-1" d="M24.42,11.61L14.42,1.59c-.38-.38-.88-.59-1.42-.59s-1.04.21-1.42.59L1.58,11.61c-.57.57-.74,1.43-.43,2.18.31.75,1.04,1.23,1.85,1.23h.22v5.75c0,2.33,1.89,4.23,4.22,4.23h11.11c2.33,0,4.22-1.9,4.22-4.23v-5.74h.22c.81,0,1.54-.49,1.85-1.23.31-.75.14-1.61-.43-2.18ZM15,23h-4s0-6,0-6c0-.55.45-1,1-1h2c.55,0,1,.45,1,1v6ZM20.78,13.03v7.74c0,1.23-1,2.23-2.22,2.23h-1.55s0-6,0-6c0-1.65-1.35-3-3-3h-2c-1.65,0-3,1.35-3,3v6h-1.56c-1.23,0-2.22-1-2.22-2.23v-7.75h-2.22L13,3l10,10.03h-2.22Z"/>
                        </svg>
                        HOME
                    </span>
                </a></li>

                <li id="dashboard" class="slider hover:border-l-4 border-[#49b5d6] cursor-pointer"><a href="/dashboard">
                    <span class="flex flex-row items-center gap-6 p-4" >
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" viewBox="0 0 26 26" width="24px" height="24px" class="min-w-[24px] min-h-[24px]">
                            <path id="Dashboard" class="cls-1" d="M14.25,3v20h-2.5V3h2.5M23,9v14h-2.5v-14h2.5M5.5,15v8h-2.5v-8h2.5M14.25,1h-2.5c-1.1,0-2,.9-2,2v20c0,1.1.9,2,2,2h2.5c1.1,0,2-.9,2-2V3c0-1.1-.9-2-2-2h0ZM23,7h-2.5c-1.1,0-2,.9-2,2v14c0,1.1.9,2,2,2h2.5c1.1,0,2-.9,2-2v-14c0-1.1-.9-2-2-2h0ZM5.5,13h-2.5c-1.1,0-2,.9-2,2v8c0,1.1.9,2,2,2h2.5c1.1,0,2-.9,2-2v-8c0-1.1-.9-2-2-2h0Z"/>
                        </svg>
                        DASHBOARD
                    </span>
                </a></li>

                <li id="readings" class="slider hover:border-l-4 border-[#49b5d6] cursor-pointer"><a href="/readings">
                    <span class="flex flex-row items-center gap-6 p-4" >
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" class="fill-[#fff] min-w-[24px] min-h-[24px]" width="24px" height = 24px>
                            <path id="Readings" class="cls-1" d="M23,4.43v17.13c-.03,1.92-1.58,3.44-3.48,3.44H6.47c-1.88,0-3.44-1.53-3.47-3.43v-8.57c0-.55.45-1,1-1s1,.45,1,1v8.56c.01.79.67,1.44,1.48,1.44h13.01c.86,0,1.5-.64,1.51-1.46V4.44c-.01-.8-.66-1.42-1.5-1.44h-4.5c-.55,0-1-.45-1-1s.45-1,1-1h4.48s.03,0,.05,0c1.88,0,3.44,1.53,3.47,3.43ZM17,6.58h-2c-.55,0-1,.45-1,1s.45,1,1,1h2c.55,0,1-.45,1-1s-.45-1-1-1ZM17,12h-8c-.55,0-1,.45-1,1s.45,1,1,1h8c.55,0,1-.45,1-1s-.45-1-1-1ZM17,17.42h-8c-.55,0-1,.45-1,1s.45,1,1,1h8c.55,0,1-.45,1-1s-.45-1-1-1ZM3.01,4.49c0-1.92,1.57-3.49,3.49-3.49s3.49,1.56,3.49,3.49c0,.6-.17,1.15-.43,1.64l.98.99c.39.39.39,1.02,0,1.41-.2.19-.45.29-.7.29s-.51-.1-.71-.29l-.98-.99c-.49.26-1.05.43-1.64.43-1.92,0-3.49-1.56-3.49-3.49ZM5.01,4.49c0,.82.67,1.49,1.49,1.49s1.49-.67,1.49-1.49-.67-1.49-1.49-1.49-1.49.67-1.49,1.49Z"/>
                        </svg>
                        READINGS
                    </span>
                </a></li>

                <li id="logs" class="slider hover:border-l-4 border-[#49b5d6] cursor-pointer"><a href="/logs">
                    <span class="flex flex-row items-center gap-6 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" fill="#FFFFFF" viewBox="0 0 26 26" width="24px" height="24px" class="min-w-[24px] min-h-[24px]">
                            <path id="Logs" class="cls-1" d="M22.41,6.59l-5-5c-.38-.38-.88-.59-1.41-.59H7.25c-2.31,0-4.22,1.88-4.25,4.19v15.58c.03,2.34,1.94,4.22,4.25,4.22,0,0,.05,0,.06,0h11.4c2.34,0,4.25-1.88,4.29-4.19v-12.81c0-.53-.21-1.04-.59-1.41ZM20,7h-3v-3l3,3ZM21,20.78c-.02,1.23-1.02,2.22-2.25,2.22-.01,0-.02,0-.03,0H7.29s-.02,0-.03,0c-1.23,0-2.24-.99-2.25-2.22V5.22c.02-1.23,1.02-2.22,2.25-2.22.01,0,.02,0,.03,0h7.71v4c0,1.1.9,2,2,2h4v11.78ZM8,7.58c0-.55.45-1,1-1h2c.55,0,1,.45,1,1s-.45,1-1,1h-2c-.55,0-1-.45-1-1ZM17,12c.55,0,1,.45,1,1s-.45,1-1,1h-8c-.55,0-1-.45-1-1s.45-1,1-1h8ZM18,18.42c0,.55-.45,1-1,1h-2c-.55,0-1-.45-1-1s.45-1,1-1h2c.55,0,1,.45,1,1Z"/>
                        </svg>
                        LOGS
                    </span>
                </a></li>
                    
                <li id="settings" class=" slider hover:border-l-4 border-[#49b5d6] cursor-pointer"><span class="">
                    <span class="flex flex-row items-center gap-6 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Path" viewBox="0 0 26 26" fill="#FFFFFF" width="24px" height="24px" class="min-w-[24px] min-h-[24px]">
                            <path id="Settings" class="cls-1" d="M13,3c.15,0,.3.02.45.05.7.17,1.24.71,1.41,1.41.21.88,1,1.47,1.86,1.47.15,0,.3-.02.45-.05.19-.05.38-.12.55-.23.31-.19.66-.28.99-.28.65,0,1.28.33,1.64.92.37.61.37,1.38,0,1.99-.55.9-.26,2.08.64,2.63.17.1.35.18.54.23,1.03.25,1.66,1.28,1.41,2.31-.17.7-.71,1.24-1.41,1.41-1.03.25-1.66,1.29-1.41,2.31.05.19.12.38.23.54.55.9.26,2.08-.64,2.63-.31.19-.65.28-1,.28s-.69-.09-1-.28c-.31-.19-.66-.28-.99-.28-.65,0-1.28.33-1.64.92-.1.17-.18.35-.23.54-.21.88-1,1.46-1.86,1.46-.15,0-.3-.02-.45-.05-.7-.17-1.24-.71-1.41-1.41-.21-.88-1-1.46-1.86-1.46-.15,0-.3.02-.45.05-.19.05-.38.12-.55.23-.31.19-.66.28-.99.28-.65,0-1.28-.33-1.64-.92-.37-.61-.37-1.38,0-1.99.55-.9.26-2.08-.64-2.63-.17-.1-.35-.18-.54-.23-1.03-.25-1.66-1.28-1.41-2.31.17-.7.71-1.24,1.41-1.41,1.03-.25,1.66-1.29,1.41-2.31-.05-.19-.12-.38-.23-.54-.55-.9-.26-2.08.64-2.63.31-.19.65-.28,1-.28s.69.09,1,.28c.31.19.66.28,1,.28.65,0,1.28-.33,1.64-.92.1-.17.18-.35.23-.55h0c.21-.88,1-1.46,1.86-1.46M13,1h0c-1.78,0-3.32,1.18-3.78,2.89-.56-.34-1.24-.53-1.94-.53s-1.42.2-2.04.57c-.89.54-1.52,1.4-1.77,2.42-.24.98-.09,1.99.4,2.86-1.38.38-2.43,1.46-2.77,2.85-.25,1.02-.08,2.07.46,2.96.52.86,1.34,1.47,2.31,1.74-.71,1.24-.69,2.75.06,3.98.72,1.18,1.97,1.88,3.35,1.88.68,0,1.35-.18,1.94-.51.38,1.38,1.46,2.43,2.85,2.77.3.07.61.11.92.11,1.78,0,3.32-1.18,3.78-2.89.56.34,1.24.53,1.94.53s1.42-.2,2.04-.57c.89-.54,1.52-1.4,1.77-2.42.24-.98.09-1.99-.4-2.86,1.38-.38,2.43-1.46,2.77-2.85.25-1.02.08-2.07-.46-2.96-.52-.86-1.34-1.47-2.31-1.74.71-1.24.69-2.75-.06-3.98-.72-1.18-1.97-1.88-3.35-1.88-.68,0-1.35.18-1.94.51-.38-1.38-1.46-2.43-2.85-2.77-.3-.07-.61-.11-.92-.11h0ZM13,10c1.66,0,3,1.34,3,3s-1.34,3-3,3-3-1.34-3-3,1.34-3,3-3M13,8c-2.76,0-5,2.24-5,5s2.24,5,5,5,5-2.24,5-5-2.24-5-5-5h0Z"/>
                        </svg>
                        SETTINGS
                    </span>
                </span></li> 
            </ul>
            <div id="NavButton" class="left-10 absolute transition-all duration-300 ease-in-out z-5">
                <button isExpanded="false" id="ExpandMenu" class="bg-[#006088] pl-3 pr-3 pt-2 pb-2 rounded-full border-2 cursor-pointer transition hover:bg-[#01445f]">
                    <h3 id="NavButtonText">>></h3>
                </button>
            </div>
        </div>
        <div id="InnerSettingsMenu" class="absolute z-4 bg-[#2a7897]/80 top-0 transition-all duration-300 left-16 w-0 opacity-0 h-full backdrop-blur-md overflow-y-scroll">
            <!--Search bar-->
            <span class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 30 30" stroke="#FFFFFF" fill="#FFFFFF" class="mt-4">
                    <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                </svg>
                <livewire:components.underline-input id="SettingSearchBar" borderColor="border-[#44a9c8]" placeholder="Search" customStyles="text-white"></livewire:components.underline-input>
            </span>
            <!--Content-->
            <div id="list" class="flex flex-col gap-2 z-4">
                <h2 class="text-[#4bbedb] pt-12 pl-4 font-semibold">INFORMATION</h2>
                <a href="/settings/deviceInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Devices</h3></button></a>
                <a href="/settings/deviceTypeInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Device Types</h3></button></a>
                <a href="/settings/sensorTypeInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Sensor Types</h3></button></a>
                <a href="/settings/sensorInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Sensors</h3></button></a>
                <a href="/settings/sensorDataTypeInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Sensor Data Types</h3></button></a>
                <a href="/settings/locationInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Locations</h3></button></a>
                <a href="/settings/subLocationInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Sub-Locations</h3></button></a>
                <a href="/settings/applicationInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Applications</h3></button></a>
                <a href="/settings/organizationInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Organizations</h3></button></a>
                <a href="/settings/softwareComponentInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Software Components</h3></button></a>
                <a href="/settings/resourceInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Resources</h3></button></a>
                <a href="/settings/permissionInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Permissions</h3></button></a>
                <a href="/settings/roleInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Roles</h3></button></a>
                <a href="/settings/userInfo"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Users</h3></button></a>
                <h2 class="text-[#4bbedb] pt-12 pl-4 font-semibold">ASSOCIATIONS</h2>
                <a href="/settings/applicationSensorTypeAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Application-Sensor Type</h3></button></a>
                <a href="/settings/applicationDeviceAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Application-Device</h3></button></a>
                <a href="/settings/applicationLocationAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Application-Location</h3></button></a>
                <a href="/settings/deviceSensorAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Device-Sensor</h3></button></a>
                <a href="/settings/rolePermissionAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Role-Permission</h3></button></a>
                <a href="/settings/userRoleAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">User-Role</h3></button></a>
                <a href="/settings/sensorDataTypeAssociation"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Sensor-Data Types</h3></button></a>
                <h2 class="text-[#4bbedb] pt-12 pl-4 font-semibold">DEPLOYMENT</h2>
                <a href="/settings/deviceDeployement"><button class="hover:bg-[#054863]/50 p-2 pl-4 rounded-lg w-full text-start text-sm cursor-pointer"><h3 class="text-white text-sm">Device Deployment</h3></button></a>
            </div>
        </div>
</nav>