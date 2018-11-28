<?php
    namespace mywishlist\controleurs;

    class ControleurAccueil{

        private $reponse, $view;

        public function __construct(\Slim\Http\Response $reponse, \Slim\Views\Twig $view)
        {
            $this->reponse = $reponse;
            $this->view = $view;
        }

        public function afficherAccueil()
        {
            return $this->view->render($this->reponse, "accueil.html");
        }
    }
