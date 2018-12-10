<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;


 class ControleurListeCreateur extends Controleur{



 	public function afficherListe($idListe){
 		$liste = Liste::find($idListe);
 		return $this->view->render($this->reponse, "affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($idListe){
 		$liste = Liste::where('id', '=', $idListe)->first();
 		$listeIt = $liste->items();
 		return $this->view->render($this->reponse, "affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}

 }