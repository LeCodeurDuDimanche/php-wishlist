<?php
    namespace mywishlist\controleurs;

    use mywishlist\models\Utilisateur;

    class Authentification {

        public static function creerCompte(string $nom, string $mdp) : int
        {
            $u = new Utilisateur();
            $u->nom = $nom;
            var_dump($mdp, password_hash($mdp, PASSWORD_DEFAULT));
            $u->mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $id = $u->save();

            if (! static::connexion($nom, $mdp))
                $id = -1;

            return $id;
        }

        public static function connexion(string $nom, string $mdp) : bool
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();

            if (static::estConnecte())
                return true;

            $user = Utilisateur::where("nom", "=", $nom)->first();
            if ($user != null && password_verify($mdp, $user->password))
            {
                $_SESSION['user'] = array( "id" => $user->id, "nom" => $user->nom);
                return true;
            }

            return false;
        }

        public static function deconnexion()
        {
            unset($_SESSION['user']);
        }

        public static function getNomUtilisateur() : string
        {
            return $_SESSION['user']['nom'];
        }

        public static function getUtilisateur() : Utilisateur
        {
            if (!static::estConnecte())
                return null;

            return Utilisateur::where("id", "=", $_SESSION['user']['id'])->first();
        }

        public static function estConnecte() : bool
        {
            return isset($_SESSION['user']);
        }
    }
