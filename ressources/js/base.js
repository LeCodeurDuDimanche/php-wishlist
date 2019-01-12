//Quand le DOM est pret a etre manipule
$(document).ready((e) => {
    //On affecte a tous les wrappers leur image generee par PHP
    $(".wrapper").each((i, o) => $(o).css("background-image", "url(" +$(o).data("background") + ")"));

    //Formulaire upload image item, affichage de l'input text ou input file selon l'etat de l'input choixImage
    $("input[name=choixImage]").change(function(e){
    	let elem = $(e.delegateTarget);

    	let val = elem.val();

    	let url = $("#image-url");
    	let upload = $("#image-upload");

    	if (val === "Url" /*&& upload.is(":visible")*/)
    	{
    		upload.stop(true).fadeOut();
    		url.stop(true).delay(400).fadeIn();
    	}
    	else if (val === "Upload" /*&& url.is(":visible")*/)
    	{
    		url.stop(true).fadeOut();
    		upload.stop(true).delay(400).fadeIn();
    	}

    });

    $("input[name=choixChangement]").change(function(e){
        let elem = $(e.delegateTarget);

        let checkbox = $("#checkbox");
        let url = $("#byUrl");
        let upload = $("#byUpload");
        let inputupload = $("#image-upload");
        let inputurl = $("#image-url");
        if (checkbox.is(":checked"))
        {
            upload.stop(true).fadeIn();
            url.stop(true).fadeIn();
            inputupload.stop(true).fadeIn();
        } else {
            if(inputupload.is(":visible"))
                inputupload.stop(true).fadeOut();
            else if (inputurl.is(":visible"))
                inputurl.stop(true).fadeOut();
            upload.stop(true).fadeOut();
            url.stop(true).fadeOut();
            
        }


    });


    //Permet de copier le lien de partage dans le presse-papier
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
            message = "<i class='fa fa-check mr-2'></i>Copié dans le presse-papier !";
        } catch(ex)
        {
            classe = "alert-danger";
            message = "<i class='fa fa-times mr-2'></i>Erreur lors de la copie, copiez manuellement";
            console.error(ex)
        }
        $("#url-alert").removeClass("alert-danger").removeClass("alert-success").addClass(classe).html(message).fadeIn().delay(5000).fadeOut();
    });

    /*
    * Permet de charger le bouton twitter lors de l'ouverture de la boite de dialogue modale de partage du lien de participation
    * En effet, le bouton twitter est invisible si il est charge dans un element invisible, alors pour la 1ere ouverture on en profite
    * pour charger le bouton.
    */
    $("#modal-social").on("shown.bs.modal", function(e){
        let elem = $("#modal-social .twitter-container");
        if (! elem.html())
        {
            twttr.widgets.createShareButton(elem.data("url"), elem[0], {text: elem.data("text"), dnt: true, size: "large", hashtags: "mywishlist", showCount: "false"});
        }
    });

    //Permet d'envoyer une requete DELETE pour la suppression de la liste
    $("#supprimer-liste").click(function(e) {
        let elem = $(e.delegateTarget);
        $.ajax(elem.data("url"), {
            method : "DELETE",
            complete: function(data, s){
                let redirect = elem.data("redirect-url");
                if (s === 'success' && redirect)
                    document.location = redirect;
            }
        });
    });

    //Permet de mettre a jour l'attribut value des inputs en fonction de leur valeur réelle, pour que les styles css s'appliquent correctement
    $(".follow-input").keyup(function(e){
        let elem = $(e.delegateTarget);
        elem.attr("value", elem.val());
    });
    $(".follow-input").each(function(){
        let elem = $(this);
        elem.attr("value", elem.val());
    });

    //Contrainte de validation des confirmations de mdp
    $("#mdp_conf").keyup(function(e){
        let elem = $(e.delegateTarget);
        let orig = $("#" + elem.data('target'));

        if (elem.val() != orig.val())
            elem[0].setCustomValidity("Le mot de passe doit correspondre");
        else
            elem[0].setCustomValidity("");
    });

    //Faire un simil-radio button avec des btn-group
    $(".btn-group.options .btn").click(function(e){
        let btn = $(e.delegateTarget);
        let parent = btn.closest(".btn-group.options");
        let val = btn.data("val");
        let input = $("#" + parent.data("target"));

        //On set la valeur
        input.val(val);
        //On set le focus sur le bouton
        parent.children(".btn").addClass("disabled");
        btn.removeClass("disabled");
    });

    //Check pseudo unique
    $(".check-pseudo").keyup(function(e){
        let elem = $(e.delegateTarget);
        let feedback = $(elem.data("feedback"));
        if (elem.val().length == 0)
        {
            feedback.text("");
        }

    });
    $(".check-pseudo").change(function (e) {
        let elem = $(e.delegateTarget);
        let feedback = $(elem.data("feedback"));
        let val = elem.val();

        if (val && val != elem.data('initial'))
        {
            fetch(elem.data("url") + val)
            .then(res => res.json())
            .then(data => {

                feedback.text(data.message);
                elem[0].setCustomValidity(data.erreur === false ? "" : "Ce pseudo est indisponible");
            })
            .catch(err => {
                console.err(err);
                feedback.text("");
            });

        }
        else {
            feedback.text("");
            elem[0].setCustomValidity("");
        }
    });
});
