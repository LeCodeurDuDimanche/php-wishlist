<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use mywishlist\models\MessagesListe;
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

    public function ajouterMessagePublic($request, $response, $args) {

        $nom = Utils::getFilteredPost($request, "createur");
        $texte = Utils::getFilteredPost($request, "message");

        if ($nom === null || $texte === null ||
            strlen($message) > 2048)
        {
            Flash::flash("erreur", "Des données sont manquantes");
        }
        else{
            //Pas besoin de sanitize le token
            $token = $args['token'];
     		$liste = static::recupererListe($request, $response, $token);
            $message = new MessagesListe();

            if (Authentification::estConnecte())
                $message->user_id = Authentification::getIdUtilisateur();
            else
                $message->createur = $nom;

            $message->liste_id = $liste->id;
            $message->texte = $texte;
            if ($message->save())
            {
                Flash::flash("message", "Message ajouté à la liste");
            }
            else {
                Flash::flash("erreur", "Erreur à la création du message");
            }
        }
 		return Utils::redirect($response, "listeParticipantDetails", ['token' => $args['token']]);
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
        $liste = static::recupererListe($request, $response, $token);

        $estCreateur = false;

        //On check les cookies (avec les listes crees par un utilisateur connecte seulement si on n'est pas connecte)
        $estConnect = Authentification::estConnecte();
        $estCreateur = in_array($liste->id, Utils::getValidListesCookie($estConnect));
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

    /**
    * Permet de restreindre l'acces aux listes non validees
    */
    public static function checkListeValideeMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        $route = $request->getAttribute('route');
        $token = $route->getArgument('token');
        $liste = static::recupererListe($request, $response, $token);

        if (!$liste->estValidee())
            throw new NotFoundException($request, $response);

        return $next($request, $response);
    }
 }
