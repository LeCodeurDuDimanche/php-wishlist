<?php
    namespace mywishlist\controleurs;

    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

/**
* Classe permettant de stocker des données dans la session pour la prochaine éxécution (et uniquement la prochaine)
*/
    class Flash {

        private static $flashedData;

        /**
        *    Fonction d'initialisation interne
        */
        private static function init()
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();

            if (! isset($_SESSION["flash"]))
            {
                $_SESSION['flash'] = [];
            }
            if (self::$flashedData === null)
                self::$flashedData = [];
        }

        /**
        * Permet de savoir si une valeur est associée à la clef $name dans les données
        * Si $name est un tableau, il est interprété comme un ensemble de clef d'un tableau mutlidimensionnel
        */
        public static function has(string $name) {
            self::init();
            if (is_array($name))
            {
                $array = $_SESSION["flash"];
                foreach($name as $key)
                {
                    if (!isset($array[$key]))
                        return false;

                    $array = $array[$key];
                }
                return true;
            }
            else
                return isset($_SESSION["flash"][$name]);
        }

        /**
        * Permet de retourner la valeur associée à la clef $name, ou null si non présente
        * Si $name est un tableau, il est interprété comme un ensemble de clef d'un tableau mutlidimensionnel
        */
        public static function get(string $name) {
            self::init();
            if (!self::has($name))
                return null;

            if (is_array($name))
            {
                $value = $_SESSION["flash"];
                foreach($name as $key)
                    $value = $value[$key];

                return $value;
            }
            else
                return $_SESSION["flash"][$name];
        }

        /**
        * Permet de sauvegarder pour la prochaine éxécution la valeur $value avec la clef $key.
        * Ecrase la valeur précédente si la clef est déjà présente
        */
        public static function flash(string $key, $value)
        {
            self::init();
            self::$flashedData[$key] = $value;
        }

        /**
        * Supprime toutes les données pour l'éxécution suivante
        */
        public static function clear()
        {
            self::init();
            $_SESSION['flash'] = [];
            self::$flashedData = [];
        }

        /**
        * Garde la valeur pour la clef $key pour l'éxécution suivante.
        * Si $key est null, garde toutes les valeurs, en gardant les modifications faites lors de cette éxécution
        */
        public static function reflash(string $key = null)
        {
            self::init();
            if ($key !== null)
                self::flash($key, self::get($key));
            else
                self::$flashedData = array_merge($_SESSION["flash"], self::$flashedData);
        }

        /**
        * Permet de changer d'éxécution, à appeler depuis le Middleware à la fin d'éxécution
        */
        public static function next()
        {
            self::init();
            $_SESSION['flash'] = self::$flashedData;
            self::$flashedData = [];
        }

        /**
        * Middleware PSR-7 (compatible Slim 3) permettant de faire marcher le mécanisme de flash
        * Retourne un callable qui est le middleware.
        */
        public static function flashMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
        {
            $response = $next($request, $response);
            self::next();
            return $response;
        }

        /**
        * Middleware PSR-7 permettant de sauvegarder les données POST jusqu'à la prochaine éxécution
        * Retourne un callable qui est le middleware.
        */
        public static function savePostMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
        {
            if ($_POST)
                self::flash("form", $_POST);
            $response = $next($request, $response);
            return $response;
        }

    }
