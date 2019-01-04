<?php
    namespace mywishlist\controleurs;

    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Flash {

        private static $flashedData;

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


        public static function has($name) {
            self::init();
            return isset($_SESSION["flash"][$name]);
        }

        public static function get($name) {
            self::init();
            return self::has($name) ? $_SESSION["flash"][$name] : null;
        }

        public static function flash($key, $value)
        {
            self::init();
            self::$flashedData[$key] = $value;
        }

        public static function reset()
        {
            self::init();
            $_SESSION['flash'] = self::$flashedData;
            self::$flashedData = [];
        }

        public static function middleware() : callable
        {
            return function(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
            {
                $response = $next($request, $response);
                Flash::reset();
                return $response;
            };
        }

    }
