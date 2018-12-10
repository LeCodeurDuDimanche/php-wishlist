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
$db = new Illuminate\Database\Capsule\Manager();
$db_conf = parse_ini_file(__DIR__ . "/src/conf/conf.ini");
$db->addConnection($db_conf);
$db->setAsGlobal();
$db->bootEloquent();

//Init Slim
$settings = require_once __DIR__ . "/src/conf/settings.php";
$container = new Slim\Container($settings);
$app = new Slim\App($container);


//Accueil
$app->get('/', function ($request, $response, $args) {
    $controller = new \mywishlist\controleurs\ControleurAccueil($response, $this->view);
    return $controller->afficherAccueil();
})->setName('accueil');

//listeCreateur
$app->post('/liste/creer', function ($request, $response, $args){

})->setName("creerListe");
$app->get('/liste/c{id}', function ($request, $response, $args){
    $controller = new \mywishlist\controleurs\ControleurListeCreateur($response, $this->view);
    return $controller->afficherListe($args["id"]);
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
