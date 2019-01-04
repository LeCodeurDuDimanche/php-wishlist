<?php
    namespace mywishlist\controleurs;

    class Utils{

        public static function redirect($response, $route, $args = [])
        {
            global $app;
            return $response->withRedirect($app->getContainer()->get('router')->pathFor($route, $args));
        }
    }
