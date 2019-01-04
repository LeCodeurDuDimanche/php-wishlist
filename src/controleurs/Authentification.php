<?php
    namespace mywishlist\controleurs;

    use mywishlist\models\Utilisateur;

    class Authentification {

        private static function init()
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();
        }

        public static function creerCompte(string $nom, string $mdp) : int
        {
            static::init();

            $u = new Utilisateur();
            $u->nom = $nom;
            $u->mdp = password_hash($mdp, PASSWORD_DEFAULT);

            if (! $u->save())
                return -1;

            $_SESSION['user'] = array( "id" => $u->id, "nom" => $u->nom);

            return $u->id;
        }

        public static function connexion(string $nom, string $mdp) : bool
        {
            if (static::estConnecte())
                return true;

            static::init();

            $user = Utilisateur::where("nom", "=", $nom)->first();
            if ($user != null && password_verify($mdp, $user->mdp))
            {
                $_SESSION['user'] = array( "id" => $user->id, "nom" => $user->nom);
                return true;
            }

            return false;
        }

        public static function deconnexion()
        {
            static::init();
            unset($_SESSION['user']);
        }

        public static function getNomUtilisateur() : string
        {
            static::init();
            return $_SESSION['user']['nom'];
        }

        public static function getUtilisateur() : Utilisateur
        {
            if (!static::estConnecte())
                return null;

            static::init();
            return Utilisateur::where("id", "=", $_SESSION['user']['id'])->first();
        }

        public static function estConnecte() : bool
        {
            static::init();
            return isset($_SESSION['user']);
        }
    }
