<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;


 class ControleurListeCreateur extends Controleur{


     public function afficherFormulaireCreation($request, $response, $args)
     {
         return $this->view->render($response, "createur/creerListe.html");
     }
/*
     public function afficherFormulaireAjoutItem($request, $response, $args)
     {
         return $this->view->render($response, "createur/ajouterItem.html");
     }
*/
 	public function afficherListe($request, $response, $args){
 		$liste = Liste::find($args['id']);
 		return $this->view->render($response, "createur/affichageListe.html", ["liste" => $liste]);
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$liste = Liste::where('no', '=', $args['id'])->get()[0];
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "createur/affichageListeDetails.html", ["liste" => $liste , "listeIt" => $listeIt]);
 	}

 }
