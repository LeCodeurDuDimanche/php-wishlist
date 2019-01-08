$(document).ready((e) => {
    console.log($(".wrapper"));
    $(".wrapper").each((i, o) => $(o).css("background-image", "url(" +$(o).data("background") + ")"));

    //Formulaire upload image item
    $("input[name=choixImage]").change(function(e){
    	let elem = $(e.delegateTarget);

    	let val = elem.val();

    	let url = $("#image-url");
    	let upload = $("#image-upload");

    	if (val === "Url" && upload.is(":visible"))
    	{
    		upload.stop(true).fadeOut();
    		url.stop(true).delay(400).fadeIn();
    	}
    	else if (val === "Upload" && url.is(":visible"))
    	{
    		url.stop(true).fadeOut();
    		upload.stop(true).delay(400).fadeIn();
    	}

    });
});

