<?php
return [
    'view' => function ($c) {
        $view = new \Slim\Views\Twig('src/vues', [
            'cache' => 'src/cache',
            'debug' => true
        ]);

        // Instantiate and add Slim specific extension
        $router = $c->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

        //Fonctions d'Authentification
        $view->getEnvironment()->addFunction(new Twig_Function("est_connecte", "Authentification::estConnecte"));
        $view->getEnvironment()->addFunction(new Twig_Function("get_username", "Authentification::getNomUtilisateur"));

        //Flash
        $view->getEnvironment()->addFunction(new Twig_Function("get_data", "Flash::get"));
        $view->getEnvironment()->addTest(new Twig_Test("flashed", "Flash::has"));

        return $view;
    },
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
