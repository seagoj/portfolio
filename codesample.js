var popup = false;
var id;

function load(){
    if(popup==false){
        $("#shade").css({
            "opacity": "0.7"
	});
	$("#shade").fadeIn("slow");
	$(id).fadeIn("slow");
	popup = true;
    }
}

function unload(){
    if(popup==true){
        $("#shade").fadeOut("slow");
        $(id).fadeOut("slow");
	popup = false;
    }
}

function center(){
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $(id).height();
    var popupWidth = $(id).width();

    $(id).css({
        "position": "absolute",
	"top": windowHeight/2-popupHeight/2,
	"left": windowWidth/2-popupWidth/2
    });

    //only need force for IE6
    $("#shade").css({
        "height": windowHeight
    });
}

$(document).ready(function(){
    $("#sample1").click(function(){
        id="#codesample1";
        center();
        load();
    });

    $("#sampleClose").click(function(){
        unload();
    });

    $("#shade").click(function(){
        unload();
    });
	
    $(document).keypress(function(e){
	if(e.keyCode==27 && status==1){
            unload();
	}
    });
});