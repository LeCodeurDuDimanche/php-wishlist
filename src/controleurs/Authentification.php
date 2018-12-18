<?php
    namespace mywishlist\controleurs;

    use mywishlist\models\Utilisateur;

    class Authentification {

        public function creerCompte(string $nom, string $mdp) : integer
        {
            $u = new Utilisateur();
            $u->nom = $nom;
            $u->mdp = password_hash($mdp);
            $id = $u->save();

            if (! connexion($nom, $mdp))
                $id = false;

            return $id;
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
                $_SESSION['user'] = array( "id" => $user->id, "nom" => $user->nom);
                return true;
            }

            return false;
        }

        public function deconnexion()
        {
            unset($_SESSION['user']);
        }

        public function getNomUtilisateur() : string
        {
            return $_SESSION['user']['nom'];
        }

        public function getUtilisateur() : Utilisateur
        {
            if (!$this->estConnecte())
                return null;

            return Utilisateur::where("id", "=", $_SESSION['user']['id'])->first();
        }

        public function estConnecte() : boolean
        {
            return isset($_SESSION['user']);
        }
    }
