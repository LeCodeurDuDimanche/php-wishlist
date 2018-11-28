<?php
    namespace mywishlist\controleurs;

    use mywishlist\models\Utilisateur;

    class Authentification {

        public function creerCompte(string $nom, string $mdp)
        {
            //TODO: a faire
        }

        public function connexion(string $nom, string $mdp) : boolean
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();

            if ($this->estConnecte())
                return true;

            $user = Utilisateur::where("nom", "=", $nom)->first();
            if ($user != null && password_verify($mdp, $user->password))
            {
                $_SESSION['id_utilisateur'] = $user->id;
                return true;
            }

            return false;
        }

        public function deconnexion()
        {
            unset($_SESSION['id_utilisateur']);
        }

        public function getUtilisateur() : Utilisateur
        {
            if (!$this->estConnecte())
                return null;

            return Utilisateur::where("id", "=", $_SESSION['id_utilisateur'])->first();
        }

        public function estConnecte() : boolean
        {
            return isset($_SESSION['id_utilisateur']);
        }
    }
