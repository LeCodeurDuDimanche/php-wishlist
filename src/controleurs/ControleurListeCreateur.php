<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 use mywishlist\models\Item;

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
        $idItem = filter_var($args['id'], FILTER_SANITIZE_STRING);
  		$liste = Liste::find($idItem);
        return $this->view->render($response, "createur/ajouterItem.html", compact("liste"));
     }

	 public function afficherModifItemListe($request, $response, $args)
     {
  		$liste = Liste::where('id', '=', $args['id'])->get()[0];
        $item = Item::where('id', '=', $args['num'])->first();
        return $this->view->render($response, "createur/modifierItem.html", compact("liste", "item"));
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
