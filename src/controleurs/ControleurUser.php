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

    public function deconnecter($request, $response, $args)
    {
        global $app;

        Authentification::deconnexion();
        return Utils::redirect($response, "afficherLogin");
    }

    public function login($request, $response, $args)
    {
        global $app;

        $user = $request->getParsedBodyParam("user", null);
        $mdp = $request->getParsedBodyParam("mdp", null);

        if ($user === null || $mdp === null)
            throw new Exception("Données invalides");

        $res = Authentification::connexion($user, $mdp);
        if (! $res)
        {
            Flash::flash("erreur", "Nom d'utilisateur ou mot de passe incorrect !");
            return Utils::redirect($response, "afficherLogin");
        }
        else
            return Utils::redirect($response, "compte");
    }

    public function creer($request, $response, $args)
    {
        global $app;

        $user = Utils::getFilteredPost($request, "user_new");
        $prenom = Utils::getFilteredPost($request, "prenom");
        $nom = Utils::getFilteredPost($request, "nom");
        $mdp = $request->getParsedBodyParam("mdp", null);
        $mdpConf = $request->getParsedBodyParam("mdp_conf", null);

        if ($user === null || $prenom == null || $nom == null || $mdp === null || $mdpConf == null)
            throw new Exception("Données invalides");

        if ($mdp !== $mdpConf)
        {
            Flash::flash("erreur", "Le mot de passe et sa confirmation ne correspondent pas");
            return Utils::redirect($response, "afficherLogin");
        }

        $id = Authentification::creerCompte($user, $prenom, $nom, $mdp);
        if ($id === -1)
        {
            Flash::flash("erreur", "Impossible de créer un compte");
            return Utils::redirect($response, "afficherLogin");
        }
        else
            return Utils::redirect($response, "compte");
    }
}
