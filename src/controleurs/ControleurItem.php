<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Item;
 use mywishlist\models\Liste;

class ControleurItem extends Controleur{

	public function afficherFormulaireReservation($request, $response, $args){

	}

	public function afficherItem($request, $response, $args){
		$idItem = filter_var($args['idItem'], FILTER_SANITIZE_STRING);
		$tokenListe = filter_var($args['token'], FILTER_SANITIZE_STRING);
		$item = Item::find($idItem);
		$liste = Liste::where('tokenParticipant', '=', $tokenListe)->first();
		if($item->liste_id !== $liste->id){
			throw new \Slim\Exception\NotFoundException($request, $response);
		}
        return $this->view->render($response, "affichageItem.html", compact("item","liste"));
	}
}