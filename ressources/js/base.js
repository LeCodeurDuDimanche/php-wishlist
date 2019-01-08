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

    //Copy-share links
    $("#copy-link,#url-partage").click(function(e) {
        let classe, message;
        try{
            //On selectionne
            let elem = document.querySelector("#url-partage");
            elem.select();
            //On copie
            document.execCommand("copy");
            elem.blur();

            classe = "alert-success";
            message = "<i class='fa fa-check mr-2'></i>Lien copié avec succès !";
        } catch(ex)
        {
            classe = "alert-danger";
            message = "<i class='fa fa-times mr-2'></i>Erreur lors de la copie, copiez manuellement";
            console.error(ex)
        }
        $("#url-alert").removeClass("alert-danger").removeClass("alert-success").addClass(classe).html(message).fadeIn().delay(5000).fadeOut();
    });
});
