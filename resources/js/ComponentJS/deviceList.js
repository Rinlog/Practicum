$(document).ready(async function(){
    //triggers dropdown to close if clicked outside of
    document.addEventListener("click",function(ev){
            let parent = ev.target.parentNode;
            while (parent.id != "DeviceContainer" && parent.id != "DeviceDropDown" ){
                parent = parent.parentNode;
                if (parent === null){
                    break;
                }
            }
            if (parent === null){
                $("#DeviceDropDown").removeClass("transition ease-out duration-100");
                $("#DeviceDropDown").addClass("transition ease-in duration-75");
                $("#DeviceDropDown").addClass("transform opacity-0 scale-0");
                $("#DeviceDropDown").removeClass("transform opacity-100 scale-100");
                $("#DeviceDropDown").attr("isOpen",false);
            }
    })
})