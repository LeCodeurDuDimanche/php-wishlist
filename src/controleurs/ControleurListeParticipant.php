<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use Slim\Exception\NotFoundException;

 class ControleurListeParticipant extends Controleur{

 	public function afficherListe($request, $response, $args){
        //Pas besoin de sanitize le token
 		$token = $args['token'];
        $liste = $this->recupererListe($request, $response, $token);

 		return $this->view->render($response, "participant/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
        //Pas besoin de sanitize le token
        $token = $args['token'];
 		$liste = $this->recupererListe($request, $response, $token);
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "participant/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}

 	private function recupererListe($request, $response, $token){
 		$liste = Liste::where('tokenParticipant', '=', $token)->first();
 		if($liste === null)
 			throw new NotFoundException($request, $response);

 		return $liste;
 	}
 }
