$(document).ready(async function(){
    //triggers dropdown to close if clicked outside of
    document.addEventListener("click",function(ev){
            let parent = ev.target.parentNode;
            while (parent.id != "SensorContainer" && parent.id != "SensorDropDown" ){
                parent = parent.parentNode;
                if (parent === null){
                    break;
                }
            }
            if (parent === null){
                $("#SensorDropDown").removeClass("transition ease-out duration-100");
                $("#SensorDropDown").addClass("transition ease-in duration-75");
                $("#SensorDropDown").addClass("transform opacity-0 scale-0");
                $("#SensorDropDown").removeClass("transform opacity-100 scale-100");
                $("#SensorDropDown").attr("isOpen",false);
            }
    })
})