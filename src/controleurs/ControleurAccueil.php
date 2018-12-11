<?php
namespace mywishlist\controleurs;

 class ControleurAccueil extends Controleur{

    public function afficherAccueil($request, $response, $args)
    {
        return $this->view->render($response, "accueil.html");
    }
}
