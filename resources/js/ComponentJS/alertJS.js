import $ from 'jquery';
$(document).ready(function(e){
    setInterval(function(){
        try{
            const animated = document.getElementById("AlertModalMain");
            
            function AddEvent(ev){
                console.log(ev);
                if(ev.animationName == "fadeout"){
                    $("#AlertModal").addClass("hide");
                }
                else{
                    $("#AlertModal").removeClass("hide");
                }
            }

            if (animated.getAttribute("haseventlistener") == "false"){
                animated.addEventListener("animationend",AddEvent);
                animated.setAttribute("haseventlistener","true");
            }
        }
        catch(exception){

        }
    },200)
});