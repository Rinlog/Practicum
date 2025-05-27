import $ from 'jquery';


export function OpenAlert(){
    $("#AlertBox").removeClass("hide");
    setTimeout(function(){
        $("#AlertBox").addClass("w-[350px] md:w-[400px] lg:w-[400px] opacity-100")
        $("#AlertBox").removeClass("opacity-0 w-0")
    },25)
}
export function CloseAlert(){
    $("#AlertText").text("");
    $("#AlertBox").removeClass("w-[350px] md:w-[400px] lg:w-[400px] opacity-100")
    $("#AlertBox").addClass("opacity-0 w-0")
    setTimeout(function(){
        $("#AlertBox").addClass("hide");
    },300)
}
let Counter = 0;
let OldTimeout = "";
export function DisplayAlert(){
    //debounce
    OpenAlert();
    Counter+=1;
    let timeout = setTimeout(function(){
        CloseAlert();
        Counter-=1;
    },2000);
    if (Counter > 1){
        clearTimeout(OldTimeout);
        Counter-=1;
    }
    OldTimeout = timeout;
}
export function SetAlertText(text){
    $("#AlertText").html(text);
}