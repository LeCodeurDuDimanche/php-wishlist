<?php

use \Slim\Views\Twig;
use \Slim\Http\Uri;
use \Slim\Http\Environment;
use \Slim\Views\TwigExtension;

use mywishlist\controleurs\Authentification;
use mywishlist\controleurs\Flash;

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
        $view->getEnvironment()->addFunction(new Twig_Function("format_date", function($time, $format = "%A %e %B %Y"){
            return $time instanceof Illuminate\Support\Carbon ?
                $time->formatLocalized($format) :
                strftime($format, (new \DateTime($time))->getTimestamp());
        }));

        $view->getEnvironment()->addFilter(new Twig_Filter("time_diff", function($time){
            $dateTime = $time instanceof \DateTime ? $time : date_create($time);
            $diff = $dateTime->diff(date_create());

            $threesold = 30;

            if ($diff->y)
                $suffix = $diff->y . " an" . ($diff->y > 1 ? "s" : "");
            else if ($diff->m)
                $suffix = $diff->m . " mois";
            else if ($diff->d)
                $suffix = $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
            else if ($diff->h)
                $suffix = $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
            else if ($diff->i)
                $suffix = $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
            else if ($diff->s > $threesold)
            {
                $suffix = $diff->s . " seconde" . ($diff->h > 1 ? "s" : "");

                if ($diff->invert)
                    $prefix = "il y a";
                else
                    $prefix = "dans";
            }
            else
                return "maintenant";



            return $prefix . " " . $suffix;

        }));

        return $view;
    },
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
