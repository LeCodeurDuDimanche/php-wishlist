<?php
    namespace mywishlist\controleurs;

    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Flash {

        private static function init()
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();

            if (! isset($_SESSION["flash"]))
            {
                $_SESSION['flash'] = [];
            }

            var_dump("INIT");
            var_dump($_SESSION["flash"]);
        }


        public static function has($name) {
            self::init();
            return isset($_SESSION["flash"][$name]);
        }

        public static function get($name) {
            self::init();
            return has($name) ? $_SESSION["flash"][$name] : null;
        }

        public static function flash($key, $value)
        {
            self::init();
            $_SESSION['flash'][$key] = $value;
        }

        public static function reset()
        {
            self::init();
            $_SESSION['flash'] = [];
        }

        public static function middleware() : callable
        {
            return function(ServerRequestInterface $request, ResponseInterface $response, callable $next){
                Flash::reset();
                return $next($request, $response);
            };
        }

    }
