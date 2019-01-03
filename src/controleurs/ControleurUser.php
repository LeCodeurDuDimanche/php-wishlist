<?php
namespace mywishlist\controleurs;

 class ControleurUser extends Controleur{

     public function afficherCompte($request, $response, $args)
     {
         return $this->view->render($response, "compte/compte.html");
     }

    public function afficherLogin($request, $response, $args)
    {
        return $this->view->render($response, "compte/login.html");
    }

    public function login($request, $response, $args)
    {
        global $app;

        $user = isset($_POST["user"]) ? $_POST["user"] : null;
        $mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : null;

        if ($user === null || $mdp === null)
            throw new Exception("Données invalides");

        $res = Authentification::connexion($user, $mdp);
        if (! $res)
        {
            Flash::flash("erreur", "Nom d'utilisateur ou mot de passe incorrect !");
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("afficherLogin"));
        }
        else
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("compte"));
    }

    public function creer($request, $response, $args)
    {
        global $app;

        $user = isset($_POST["user"]) ? $_POST["user"] : null;
        $mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : null;
        $mdpConf = isset($_POST["mdp_conf"]) ? $_POST["mdp_conf"] : null;

        if ($user === null || $mdp === null)
            throw new Exception("Données invalides");

        if ($mdp !== $mdpConf)
        {
            Flash::flash("erreur", "Le mot de passe et sa confirmation ne correspondent pas");
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("afficherLogin"));
        }

        $id = Authentification::creerCompte($user, $mdp);
        if ($id === -1)
        {
            Flash::flash("erreur", "Impossible de créer un compte");
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("afficherLogin"));
        }
        else
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("compte"));
    }
}
