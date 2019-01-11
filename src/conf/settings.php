<?php

use \Slim\Views\Twig;
use \Slim\Http\Uri;
use \Slim\Http\Environment;
use \Slim\Views\TwigExtension;

use mywishlist\controleurs\Authentification;
use mywishlist\controleurs\Flash;
use mywishlist\controleurs\Utils;

setlocale(LC_TIME, 'fr', "fr_FR", "fr.utf8", "fr_FR.utf8", "fr_FR.utf-8");

return [
    'view' => function ($c) {
        $view = new Twig('src/vues', [
            'cache' => 'src/cache',
            'debug' => true
        ]);

        // Instantiate and add Slim specific extension
        $router = $c->get('router');
        $uri = Uri::createFromEnvironment(new Environment($_SERVER));
        $view->addExtension(new TwigExtension($router, $uri));

        //Fonctions d'Authentification
        $view->getEnvironment()->addFunction(new Twig_Function("est_connecte", Authentification::class."::estConnecte"));
        $view->getEnvironment()->addFunction(new Twig_Function("get_username", Authentification::class."::getNomUtilisateur"));
        $view->getEnvironment()->addFunction(new Twig_Function("get_user_id", Authentification::class."::getIdUtilisateur"));

        //Flash
        $view->getEnvironment()->addFunction(new Twig_Function("get_data", Flash::class . "::get"));
        $view->getEnvironment()->addTest(new Twig_Test("flashed", Flash::class . "::has"));

        //Session
        $view->getEnvironment()->addFunction(new Twig_Function("session", function($var){
            return isset($_SESSION[$var]) ? $_SESSION[$var] : null;
        }));
        $view->getEnvironment()->addTest(new Twig_Test("inSession", function($var){
            return isset($_SESSION[$var]);
        }));

        //Date
        $view->getEnvironment()->addFunction(new Twig_Function("format_date", Utils::class . "::formatTwigFunction"));
        $view->getEnvironment()->addFilter(new Twig_Filter("time_diff", Utils::class . "::timeDiffTwigFilter"));

        //debug
        $view->getEnvironment()->addFilter(new Twig_Filter("d", function($val){
            ob_start();
            var_dump($val);
            return ob_get_clean();
        }));

        return $view;
    },
    'notFoundHandler' => Utils::class . "::notFound",
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
