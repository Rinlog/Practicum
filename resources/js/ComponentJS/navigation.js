import $ from 'jquery';

$(document).ready(function(e){
    //setting current page through applyings styles so it is easy to see what page we are on
    const location = window.location.href;
    console.log(location);
    if (location.includes("/home")){
        $("#home").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#home").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else if (location.includes("/applications")){
        $("#apps").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#apps").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else if (location.includes("/dashboard")){
        $("#dashboard").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#dashboard").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else if (location.includes("/readings")){
        $("#readings").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#readings").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else if (location.includes("/logs")){
        $("#logs").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#logs").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else if (location.includes("/settings")){
        $("#settings").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#settings").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    else{
        $("#home").removeClass("hover:border-l-4 border-[#49b5d6]");
        $("#home").addClass("border-l-4 border-[#e2c22f] bg-[#00719d]")
    }
    //this is used to open or close the side navigation bar
    $("#ExpandMenu").on("click",function(e){
        if (e.currentTarget.attributes["isexpanded"].nodeValue == "false"){
            $("#SideNav").removeClass("w-16")
            $("#NavButton").removeClass("left-10");
            $("#SideNavFrame").removeClass("w-16");
            $("#InnerSettingsMenu").removeClass("left-16");

            $("#SideNav").addClass("w-64")
            $("#SideNavFrame").addClass("w-64");
            $("#NavButton").addClass("left-57");
            $("#InnerSettingsMenu").addClass("left-64");

            if (location.includes("/settings")){
                SettingsMenuCustom("small");
            }
            UserControlCorrection("left");
            $("#NavButtonText").text("<<");
            $("#ExpandMenu").attr("isexpanded",true);
        }
        else{
            $("#SideNav").addClass("w-16")
            $("#NavButton").addClass("left-10");
            $("#SideNavFrame").addClass("w-16");
            $("#InnerSettingsMenu").addClass("left-16");

            $("#SideNav").removeClass("w-64")
            $("#SideNavFrame").removeClass("w-64");
            $("#NavButton").removeClass("left-57");
            $("#InnerSettingsMenu").removeClass("left-64");

            if (location.includes("/settings")){
                SettingsMenuCustom("big");
            }
            UserControlCorrection("normal");
            $("#NavButtonText").text(">>");

            $("#ExpandMenu").attr("isexpanded",false);
        }
    })
    //display second nav on settings hover
    $("#settings").hover(function(e){
        if (e.type == "mouseenter"){
            $("#InnerSettingsMenu").removeClass("w-0 opacity-0");
            $("#InnerSettingsMenu").addClass("p-4 w-80 opacity-100");
        }
    });
    $("#settings").click(function(e){
        if ($("#InnerSettingsMenu").hasClass("p-4 w-80 opacity-100")){
            $("#InnerSettingsMenu").addClass("w-0 opacity-0");
            $("#InnerSettingsMenu").removeClass("p-4 w-80 opacity-100");
        }
        else{
            $("#InnerSettingsMenu").removeClass("w-0 opacity-0");
            $("#InnerSettingsMenu").addClass("p-4 w-80 opacity-100");
        }
    })
    document.addEventListener("click",function(ev){
        let CurrentParent = ev.target.parentNode;
        while (CurrentParent.id != "SideNav"){
            CurrentParent = CurrentParent.parentNode
            if (CurrentParent == null){
                break;
            }
        }
        //if it is null i clicked outside of the nav
        if (CurrentParent == null){
            $("#InnerSettingsMenu").addClass("w-0 opacity-0");
            $("#InnerSettingsMenu").removeClass("p-4 w-80 opacity-100");
        }
    })
    function SettingsMenuCustom(smallBig){
        if (smallBig == "small"){
            $("#MainWindowSettings").removeClass("lg:w-[1750px]");
            $("#MainWindowSettings").addClass("lg:w-[1500px]");
        }
        else if (smallBig == "big"){
            setTimeout(function(){
                $("#MainWindowSettings").addClass("lg:w-[1750px]");
                $("#MainWindowSettings").removeClass("lg:w-[1500px]");
            },170);
        }
    }
    function UserControlCorrection(leftNorm){
        if (leftNorm == "left"){
            $("#UserControlDiv").removeClass("pr-25");
            $("#UserDropDown").removeClass('right-25');
            $("#UserControlDiv").addClass("pr-70");
            $("#UserDropDown").addClass('right-70');
        }
        else if (leftNorm == "normal"){
            $("#UserControlDiv").addClass("pr-25");
            $("#UserDropDown").addClass('right-25');
            $("#UserControlDiv").removeClass("pr-70");
            $("#UserDropDown").removeClass('right-70');
        }
    }
    $("#SettingSearchBar").on("input",function(e){
        let search = e.target.value;
        let regex = new RegExp("^"+search+".*","i")
        $("#list").children().each(function(index){
            if ($(this).prop("tagName") == "A"){
                let TestCase = $(this).text();
                if (regex.test(TestCase) == false){
                    $(this).addClass("hide");
                }
                else{
                    $(this).removeClass("hide");
                }
            }
        })
    })
})