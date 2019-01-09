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

        public static function creerCompte(string $pseudo, string $nom, string $prenom, string $mdp) : int
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

            $user = Utilisateur::where("pseudo", "=", $pseudo)->first();
            if ($user != null && password_verify($mdp, $user->mdp))
            {
                $_SESSION['user'] = array( "id" => $user->id, "pseudo" => $user->pseudo);
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

        public static function getIdUtilisateur() : int
        {
            static::init();
            if (!static::estConnecte())
                return -1;

            return intval($_SESSION['user']['id']);
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
