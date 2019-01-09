<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 class ControleurAccueil extends Controleur{

    public function afficherAccueil($request, $response, $args)
    {
        return $this->view->render($response, "accueil.html");
    }

    public function afficherListesPubliques($request, $response, $args){
    	$nbParPage = 20;

    	$numPage = 1;
    	if($args['numPage'] !== null){
    		$numPage = intval($args['numPage']);
    	}

    	$listes = Liste::where("estPublique", "=", "1")->take($nbParPage)->skip($numPage-1*$nbParPage)->get();

    	return $this->view->render($response, "listesPubliques.html", compact("listes"));
    }
}
