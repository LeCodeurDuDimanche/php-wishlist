<?php
namespace mywishlist\controleurs;

 class ControleurAccueil extends Controleur{
    
    public function afficherAccueil()
    {
        return $this->view->render($this->reponse, "accueil.html");
    }
}
