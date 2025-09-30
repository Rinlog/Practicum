$(document).ready(async function(){
    //triggers dropdown to close if clicked outside of
    document.addEventListener("click",function(ev){
            let parent = ev.target.parentNode;
            while (parent.id != "LocationContainer" && parent.id != "locationDropDown" ){
                parent = parent.parentNode;
                if (parent === null){
                    break;
                }
            }
            if (parent === null){
                $("#locationDropDown").removeClass("transition ease-out duration-100");
                $("#locationDropDown").addClass("transition ease-in duration-75");
                $("#locationDropDown").addClass("transform opacity-0 scale-0");
                $("#locationDropDown").removeClass("transform opacity-100 scale-100");
                $("#locationDropDown").attr("isOpen",false);
            }
    })
})