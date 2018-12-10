<?php

 namespace mywishlist\controleurs;

class Controleur {

 	protected $reponse, $view;

    public function __construct(\Slim\Http\Response $reponse, \Slim\Views\Twig $view) {
        $this->reponse = $reponse;
        $this->view = $view;
    }
}