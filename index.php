<?php
/**
 * File:  index.php
 * Creation Date: 04/12/2017
 * description:
 *
 * @author: canals
 */

require_once __DIR__ . '/vendor/autoload.php';

//Init Eloquent

//Init Slim
$app = new Slim\App();

//Init Twig
// Fetch DI Container
$container = $app->getContainer();
// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('src/vues', [
        'cache' => 'src/cache',
        'debug' => true
    ]);

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};


//Accueil
$app->get('/', function ($request, $response, $args) {
    $controller = new \mywishlist\controleurs\ControleurAccueil($response, $this->view);
    return $controller->afficherAccueil();
})->setName('accueil');

$app->get('/liste/c{id}', function ($request, $response, $args){
    $controller = new \mywishlist\controleurs\ControleurListeCreateur();
    return $controller->afficherListe($args[0]);
})->setName('listeCreateur');

$app->get('/liste/c{id}/details',function ($request, $response, $args){
    $controller = new \mywishlist\controleurs\ControleurListeCreateur();
    return $controller->afficherListeAvecDetails($args[0]);
})->setName('listeCreateurDetails');

$app->get('/liste/p{id}', function ($request, $response, $args){
    $controller = new \mywishlist\controleurs\ControleurListeParticipant();
    return $controller->afficherListe($args[0]);
})->setName('listeParticipant');

$app->get('/liste/p{id}/details',function ($request, $response, $args){
    $controller = new \mywishlist\controleurs\ControleurListeParticipant();
    return $controller->afficherListeAvecDetails($args[0]);
})->setName('listeParticipantDetails');

$app->get('/item/{id}', function ($request, $response, $args){

});

$app->run();
