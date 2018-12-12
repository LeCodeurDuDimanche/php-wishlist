<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;


 class ControleurListeCreateur extends Controleur{


     public function afficherFormulaireCreation($request, $response, $args)
     {
         return $this->view->render($response, "createur/creerListe.html");
     }

     public function creerListe($request, $response, $args)
     {

     }


     public function afficherFormulaireAjoutItem($request, $response, $args)
     {
  		$liste = Liste::find($args['id']);
        return $this->view->render($response, "createur/ajouterItem.html", compact("liste"));
     }

	 public function afficherModifItemListe($request, $response, $args)
     {
  		
        return $this->view->render($response, "createur/modifierItem.html", compact("liste"));
     }     

 	public function afficherListe($request, $response, $args){
 		$liste = Liste::find($args['id']);
 		return $this->view->render($response, "createur/affichageListe.html", compact("liste"));
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$liste = Liste::where('id', '=', $args['id'])->get()[0];
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "createur/affichageListeDetails.html", compact("liste", "listeIt"));
 	}

 }
