import $ from 'jquery';

$(document).ready(function(e){
    $("#OpenUserControls").on("click",function(e){
        if ($("#UserDropDown").attr("isOpen") == "false"){
            $("#UserDropDown").addClass("transition ease-out duration-100");
            $("#UserDropDown").removeClass("transform opacity-0 scale-0");
            $("#UserDropDown").addClass("transform opacity-100 scale-100");
            $("#UserDropDown").attr("isOpen",true);
        }
        else{
            $("#UserDropDown").removeClass("transition ease-out duration-100");
            $("#UserDropDown").removeClass("transform opacity-100 scale-100");
            $("#UserDropDown").addClass("transition ease-in duration-75");
            $("#UserDropDown").addClass("transform opacity-0 scale-0");
            $("#UserDropDown").attr("isOpen",false);
        }
    });
    //triggers dropdown to close if clicked outside of
    document.addEventListener("click",function(ev){
        let parent = ev.target.parentNode
        if ($("#UserDropDown").attr("isOpen") == "true"){
            if (parent.id != "UserControlDiv" && parent.id != "OpenUserControls"){
                $("#OpenUserControls").trigger("click");
            }
        }
    })
})