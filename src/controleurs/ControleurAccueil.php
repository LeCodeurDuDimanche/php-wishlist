<?php
    namespace mywishlist\controleurs;

    class ControleurAccueil{

        private $reponse, $view;

        public function __construct($reponse, $view)
        {
            $this->reponse = $reponse;
            $this->view = $view;
        }

        public function afficherAccueil($nom)
        {
            return $this->view->render($this->reponse, 'accueil.html', [
                  'nom' => $nom
              ]);
        }
    }
