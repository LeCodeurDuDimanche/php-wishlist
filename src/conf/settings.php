<?php

use \Slim\Views\Twig;
use \Slim\Http\Uri;
use \Slim\Http\Environment;
use \Slim\Views\TwigExtension;

use mywishlist\controleurs\Authentification;
use mywishlist\controleurs\Flash;

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

        //Flash
        $view->getEnvironment()->addFunction(new Twig_Function("get_data", Flash::class . "::get"));
        $view->getEnvironment()->addTest(new Twig_Test("flashed", Flash::class . "::has"));

        return $view;
    },
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
