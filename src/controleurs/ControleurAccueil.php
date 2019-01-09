<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 class ControleurAccueil extends Controleur{

    public function afficherAccueil($request, $response, $args)
    {
        return $this->view->render($response, "accueil.html");
    }

    public function afficherListesPubliques($request, $response, $args){
    	return $this->view->render($response, "listesPubliques.html", compact("listes"));
    }
}
