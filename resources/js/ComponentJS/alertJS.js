import $ from 'jquery';


export function OpenAlert(){
    $("#AlertBox").removeClass("hide");
    setTimeout(function(){
        $("#AlertBox").removeClass("opacity-0 min-w-0")
        $("#AlertBox").addClass("min-w-100 opacity-100")
    },25)
}
export function CloseAlert(){
    $("#AlertText").text("");
    $("#AlertBox").removeClass("min-w-100 opacity-100")
    $("#AlertBox").addClass("opacity-0 min-w-0")
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
    },2500);
    if (Counter > 1){
        clearTimeout(OldTimeout);
        Counter-=1;
    }
    OldTimeout = timeout;
}
export function SetAlertText(text){
    $("#AlertText").html(text);
}