<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 class ControleurListeParticipant extends Controleur{

 	public function afficherListe($request, $response, $args){
        $liste = Liste::find($args['id']);
 		return $this->view->render($response, "participant/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$liste = Liste::where('no', '=', $args["id"])->get()[0];
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "participant/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}
 }
