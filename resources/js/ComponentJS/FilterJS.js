$(document).ready(async function(){
    $("#Filter").click(function(e){
        if ($("#FilterDropDown").attr("isOpen") == "false"){
            $("#FilterDropDown").addClass("transition ease-out duration-100");
            $("#FilterDropDown").removeClass("transform opacity-0 scale-0");
            $("#FilterDropDown").addClass("transform opacity-100 scale-100");
            $("#FilterDropDown").attr("isOpen",true);
            $("#startTime").val("00:00");
            $("#endTime").val("23:59");
        }
        else{
            $("#FilterDropDown").removeClass("transition ease-out duration-100");
            $("#FilterDropDown").addClass("transition ease-in duration-75");
            $("#FilterDropDown").addClass("transform opacity-0 scale-0");
            $("#FilterDropDown").removeClass("transform opacity-100 scale-100");
            $("#FilterDropDown").attr("isOpen",false);
        }
    });

    //triggers dropdown to close if clicked outside of
    document.addEventListener("click",function(ev){
            let parent = ev.target.parentNode;
            while (parent.id != "FilterContainer" && parent.id != "FilterDropDown" ){
                parent = parent.parentNode;
                if (parent === null){
                    break;
                }
            }
            if (parent === null){
                $("#FilterDropDown").removeClass("transition ease-out duration-100");
                $("#FilterDropDown").addClass("transition ease-in duration-75");
                $("#FilterDropDown").addClass("transform opacity-0 scale-0");
                $("#FilterDropDown").removeClass("transform opacity-100 scale-100");
                $("#FilterDropDown").attr("isOpen",false);
            }
    })
})