<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 class ControleurListeParticipant extends Controleur{

 	public function afficherListe($request, $response, $args){
 		$token = filter_var($args['token'], FILTER_SANITIZE_STRING);
        $liste = $this->recupererListe($request, $response, $token);
 		return $this->view->render($response, "participant/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$token = filter_var($args['token'], FILTER_SANITIZE_STRING);
 		$liste = $this->recupererListe($request, $response, $token);
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "participant/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}

 	private function recupererListe($request, $response, $token){
 		$liste = Liste::where('tokenParticipant', '=', $token)->first();
 		if(empty($liste)){
 			throw new \Slim\Exception\NotFoundException($request, $response);
 		}
 		return $liste;
 	}
 }
