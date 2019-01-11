<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use Slim\Exception\NotFoundException;
 use Psr\Http\Message\ServerRequestInterface;
 use Psr\Http\Message\ResponseInterface;

 class ControleurListeParticipant extends Controleur{

 	public function afficherListe($request, $response, $args){
        //Pas besoin de sanitize le token
 		$token = $args['token'];
        $liste = static::recupererListe($request, $response, $token);

 		return $this->view->render($response, "participant/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
        //Pas besoin de sanitize le token
        $token = $args['token'];
 		$liste = static::recupererListe($request, $response, $token);
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "participant/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}

 	private static function recupererListe($request, $response, $token){
 		$liste = Liste::where('tokenParticipant', '=', $token)->first();
 		if($liste === null)
 			throw new NotFoundException($request, $response);

 		return $liste;
 	}

    /**
    * Permet de filtrer le createur de la liste
    */
    public static function checkNonCreateurMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        $route = $request->getAttribute('route');
        $token = $route->getArgument('token');
        $liste = static::recupererListe($request, $reponse, $token);

        $estCreateur = false;

        //On check les cookies
        $estCreateur = in_array($liste->id, Utils::getValidListesCookie());
        //On check les listes de l'Utilisateur
        if ($user = Authentification::getUtilisateur())
            $estCreateur = $estCreateur || $user->listesCrees->where("id", "=", $liste->id)->count() > 0;

        //Si l'utilisateur est createur de cette liste, on le redirige vers le lien du createur
        if ($estCreateur)
        {
            Flash::flash("avertissement", "Vous avez tenté d'accéder à la liste avec l'url de participation. Vous avez été redirigé sur la page du créateur de liste.");
            return Utils::redirect($response, "listeCreateur", ["id" => $liste->tokenCreateur]);
        }

        return $next($request, $response);
    }
 }
