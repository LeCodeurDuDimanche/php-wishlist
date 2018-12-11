<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use mywishlist\models\Item;

 class  ControleurListeParticipant extends Controleur{
 	
 	public function afficherListe($idListe){
 		$liste = Liste::find($idListe);
 		return $this->view->render($this->reponse, "affichageListeParticipant.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($idListe){
 		$liste = Liste::where('no', '=', $idListe)->get()[0];
 		$listeIt = $liste->items()->get();	
 		return $this->view->render($this->reponse, "affichageListeParticipantDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}
 }