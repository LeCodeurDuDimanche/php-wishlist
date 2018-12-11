<?php

 namespace mywishlist\controleurs;

class Controleur {

 	protected $view;

    public function __construct(\Slim\Container $container) {
        $this->view = $container->view;
    }
}
