<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 class ControleurListeParticipant extends Controleur{

 	public function afficherListe($request, $response, $args){
 		$token = filter_var($args['token'], FILTER_SANITIZE_STRING);
        $liste = Liste::where('tokenParticipant', '=', $token)->first();
 		return $this->view->render($response, "participant/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$token = filter_var($args['token'], FILTER_SANITIZE_STRING);
 		$liste = Liste::where('tokenParticipant', '=', $token)->first();
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "participant/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}
 }
