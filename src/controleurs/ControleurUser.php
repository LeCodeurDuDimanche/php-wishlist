<?php
namespace mywishlist\controleurs;

use mywishlist\models\Utilisateur;

 class ControleurUser extends Controleur{

     public function afficherCompte($request, $response, $args)
     {
         $user = Authentification::getUtilisateur();
         return $this->view->render($response, "compte/compte.html", compact("user"));
     }

    public function afficherLogin($request, $response, $args)
    {
        return $this->view->render($response, "compte/login.html");
    }

    public function deconnecter($request, $response, $args)
    {
        Authentification::deconnexion();
        return Utils::redirect($response, "afficherLogin");
    }

    public function login($request, $response, $args)
    {
        $user = $request->getParsedBodyParam("user", null);
        $mdp = $request->getParsedBodyParam("mdp", null);

        if ($user === null || $mdp === null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
            return Utils::redirect($response, "afficherLogin");
        }

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
        $user = Utils::getFilteredPost($request, "user_new");
        $prenom = Utils::getFilteredPost($request, "prenom");
        $nom = Utils::getFilteredPost($request, "nom");
        $mdp = $request->getParsedBodyParam("mdp", null);
        $mdpConf = $request->getParsedBodyParam("mdp_conf", null);

        if ($user === null || $prenom == null || $nom == null || $mdp === null || $mdpConf == null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
            return Utils::redirect($response, "afficherLogin");
        }

        if (Utilisateur::where("pseudo", "=", $user)->count())
        {
            Flash::flash("erreur", "Le pseudo $user n'est pas disponible");
            return Utils::redirect($response, "afficherLogin");
        }

        if ($mdp !== $mdpConf)
        {
            Flash::flash("erreur", "Le mot de passe et sa confirmation ne correspondent pas");
            return Utils::redirect($response, "afficherLogin");
        }

        if (! preg_match("/(?=.*\d)(?=.*[a-zA-Z]).{6,}/", $mdp))
        {
            Flash::flash("erreur", "Le mot de passe doit faire au moins 6 caractères et contenir 1 chiffre et 1 lettre");
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

    public function modifier($request, $response, $args)
    {
        $pseudo = Utils::getFilteredPost($request, "pseudo");
        $prenom = Utils::getFilteredPost($request, "prenom");
        $nom = Utils::getFilteredPost($request, "nom");

        if ($pseudo === null || $prenom == null || $nom == null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
            return Utils::redirect($response, "compte");
        }

        if ($pseudo !== Authentification::getNomUtilisateur() && Utilisateur::where("pseudo", "=", $pseudo)->count())
        {
            Flash::flash("erreur", "Le pseudo $pseudo n'est pas disponible");
            return Utils::redirect($response, "compte");
        }

        $user = Authentification::getUtilisateur();
        $user->pseudo = $pseudo;
        $user->prenom = $prenom;
        $user->nom = $nom;
        $user->save();

        Flash::flash("message", "Modification enregistrée");
        return Utils::redirect($response, "compte");
    }

    public function modifierMdp($request, $response, $args)
    {
        $mdp = $request->getParsedBodyParam("mdp", null);
        $mdpNew = $request->getParsedBodyParam("mdp_new", null);
        $mdpConf = $request->getParsedBodyParam("mdp_conf", null);

        if ($mdpNew == null || $mdp === null || $mdpConf == null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
            return Utils::redirect($response, "compte");
        }

        if ($mdpNew !== $mdpConf)
        {
            Flash::flash("erreur", "Le mot de passe et sa confirmation ne correspondent pas");
            return Utils::redirect($response, "compte");
        }

        if (! preg_match("/(?=.*\d)(?=.*[a-zA-Z]).{6,}/", $mdpNew))
        {
            Flash::flash("erreur", "Le mot de passe doit faire au moins 6 caractères et contenir 1 chiffre et 1 lettre");
            return Utils::redirect($response, "compte");
        }

        if (!Authentification::modifierMotDePasse($mdp, $mdpNew))
        {
            Flash::flash("erreur", "Le mot de passe n'est pas correcte");
            return Utils::redirect($response, "compte");
        }

        Flash::flash("message", "Modification enregistrée");
        return Utils::redirect($response, "compte");
    }

    public function estPseudoDisponible($request, $response, $args)
    {
        $pseudo = $args["pseudo"];
        if (! $pseudo)
            $ret = ["erreur" => true, "message" => "Aucun pseudo spécifié"];
        else {
            $dispo = Utilisateur::where("pseudo", "=", $pseudo)->count() > 0;
            if ($dispo)
                $ret = ["erreur" => true, "message" => "Le pseudo est déjà utilisé"];
            else
                $ret = ["erreur" => false, "message" => "Pseudo disponible"];
        }
        return $response->withJson($ret);
    }

    public function supprimerCompte($request, $response, $args)
    {

        $mdp = Utils::getFilteredPost($request, "mdp");

        if (Authentification::supprimer($mdp))
            Flash::flash('message', 'Compte supprimé. Toutes les listes et message associés à ce compte on été supprimés.');
        else
            Flash::flash('erreur', 'Mot de passe incorrect');

        return Utils::redirect($response, "compte");
    }
}
