<?php
/**
 * File:  index.php
 * Creation Date: 04/12/2017
 * description:
 *
 * @author: canals
 */

 use \mywishlist\controleurs\ControleurListeCreateur;
 use \mywishlist\controleurs\ControleurListeParticipant;
 use \mywishlist\controleurs\ControleurAccueil;
 use \mywishlist\controleurs\ControleurUser;
 use \mywishlist\controleurs\ControleurItem;

 use Illuminate\Database\Capsule\Manager as DBManager;
 use Psr7Middlewares\Middleware;

require_once __DIR__ . '/vendor/autoload.php';

//Init Eloquent
$db = new DBManager();
$db_conf = parse_ini_file(__DIR__ . "/src/conf/conf.ini");
$db->addConnection($db_conf);
$db->setAsGlobal();
$db->bootEloquent();

//Init Slim
$settings = require_once __DIR__ . "/src/conf/settings.php";
$container = new Slim\Container($settings);
$app = new Slim\App($container);

$appMiddlewares = [
    Middleware::TrailingSlash(false) //(optional) set true to add the trailing slash instead remove
        ->redirect(301),
    \mywishlist\controleurs\Flash::flashMiddleware(),
    \mywishlist\controleurs\Flash::savePostMiddleware()
];

foreach($appMiddlewares as $middleware)
{
    $app->add($middleware);
}


//Accueil
$app->get('/', ControleurAccueil::class.":afficherAccueil")->setName('accueil');

//listeCreateur
$app->group("/liste", function() use ($app){
    $app->get('/creer', ControleurListeCreateur::class.":afficherFormulaireCreation")->setName("formulaireCreerListe");
    $app->post('/creer', ControleurListeCreateur::class.":creerListe")->setName("creerListe");
    $app->get('/c{id}', ControleurListeCreateur::class.":afficherListe")->setName('listeCreateur');
    $app->get('/c{id}/details', ControleurListeCreateur::class.":afficherListeAvecDetails")->setName('listeCreateurDetails');

    $app->get('/c{id}/creerItem', ControleurListeCreateur::class.":afficherFormulaireAjoutItem")->setName("formulaireAjouterItem");
    $app->post('/c{id}/creerItem', ControleurListeCreateur::class.":ajouterItem")->setName("ajouterItem");


    $app->get('/c{id}/item{num}/editer', ControleurListeCreateur::class.":afficherModifItemListe")->setName("formulaireModifItem");
    //methode put
    $app->post('/c{id}/item{num}/editer', ControleurListeCreateur::class.":modifierItem")->setName("modifierItem");
    $app->post('/c{id}/item{num}', ControleurListeCreateur::class.":supprimerItem")->setName('supprimerItem');

});

//Liste participant
$app->group("/liste", function() use ($app){
    $app->get('/p{token}', ControleurListeParticipant::class.":afficherListe")->setName('listeParticipant');
    $app->get('/p{token}/details', ControleurListeParticipant::class.":afficherListeAvecDetails")->setName('listeParticipantDetails');
    $app->get('/p{token}/details/item/{idItem}', ControleurItem::class.":afficherItem")->setName('afficherItem');
    $app->get('/p{token}/details/item/{idItem}/reserver', ControleurItem::class.":afficherFormulaireReservation")->setName('formulaireReserverItem');
    $app->post('/p{token}/details/item/{idItem}/reserver', ControleurItem::class.":reserverItem")->setName('reserverItem');
});

//compte
$app->group("/compte", function() use ($app){
    $app->get("", ControleurUser::class . ":afficherCompte")->setName("compte");
    $app->get("/login", ControleurUser::class . ":afficherLogin")->setName("afficherLogin");
    $app->get("/deconnexion", ControleurUser::class . ":deconnecter")->setName("deconnexion");

    $app->post("/login", ControleurUser::class . ":login")->setName("login");
    $app->post("/nouveau", ControleurUser::class . ":creer")->setName("creerCompte");
});

$app->run();
