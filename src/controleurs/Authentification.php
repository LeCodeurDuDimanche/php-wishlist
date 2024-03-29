<?php
    namespace mywishlist\controleurs;

    use mywishlist\models\Utilisateur;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Authentification {

        private static function init()
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();
        }

        public static function creerCompte(string $pseudo, string $prenom, string $nom, string $mdp) : int
        {
            static::init();

            $u = new Utilisateur();
            $u->pseudo = $pseudo;
            $u->nom = $nom;
            $u->prenom= $prenom;
            $u->mdp = password_hash($mdp, PASSWORD_DEFAULT);

            if (! $u->save())
                return -1;

            $_SESSION['user'] = array( "id" => $u->id, "pseudo" => $u->pseudo);

            return $u->id;
        }

        public static function connexion(string $pseudo, string $mdp) : bool
        {
            if (static::estConnecte())
                return true;

            static::init();

            $user = static::verifierMdp($pseudo, $mdp);
            if ($user)
            {
                $_SESSION['user'] = array( "id" => $user->id, "pseudo" => $user->pseudo);
                return true;
            }

            return false;
        }

        private static function verifierMdp(string $pseudo, string $mdp)
        {
            $user = Utilisateur::where("pseudo", "=", $pseudo)->first();
            return ($user != null && password_verify($mdp, $user->mdp)) ? $user : null;
        }

        public static function modifierMotDePasse(string $mdpOld, string $mdpNew) : bool
        {
            $user = static::verifierMdp(static::getNomUtilisateur(), $mdpOld);
            if ($user)
            {
                $user->mdp = \password_hash($mdpNew, PASSWORD_DEFAULT);
                return $user->save();
            }
            return false;
        }

        public static function supprimer(string $mdp)
        {
            $user = static::verifierMdp(static::getNomUtilisateur(), $mdp);
            if ($user)
            {
                $user->delete();
                self::deconnexion();
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
            if (!static::estConnecte())
                return "";

            return $_SESSION['user']['pseudo'];
        }

        public static function getNomPrenomUtilisateur() : string
        {
            $user = self::getUtilisateur();
            return $user ? "$user->prenom $user->nom" : "";
        }

        public static function getIdUtilisateur() : int
        {
            static::init();
            if (!static::estConnecte())
                return -1;

            return intval($_SESSION['user']['id']);
        }

        public static function getUtilisateur()
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

        public static function requireLoggedMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
        {
            if (! self::estConnecte())
                return Utils::redirect($response, "afficherLogin");

            return $next($request, $response);
        }

        public static function requireAnonMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
        {
            if (self::estConnecte())
                return Utils::redirect($response, "compte");

            return $next($request, $response);
        }
    }
