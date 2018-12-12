<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Item;
 use mywishlist\models\Liste;

class ControleurItem extends Controleur{

	public function afficherFormulaireReservation($request, $response, $args){

	}

	public function afficherItem($request, $response, $args){
		$item = Item::find($args['idItem']);
		$liste = Liste::find($item->liste_id);
        return $this->view->render($response, "affichageItem.html", compact("item","liste"));
	}
}