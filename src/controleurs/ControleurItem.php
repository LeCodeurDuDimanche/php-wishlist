<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Item;
 use mywishlist\models\Liste;

class ControleurItem extends Controleur{

	public function afficherFormulaireReservation($request, $response, $args){
		$idItem = filter_var($args['idItem'], FILTER_SANITIZE_STRING);
		$tokenListe = filter_var($args['token'], FILTER_SANITIZE_STRING);
		$item = $this->recuperItem($request, $response, $idItem, $tokenListe);

		return $this->view->render($response, "reserverItem.html", compact("item"));
	}

	public function afficherItem($request, $response, $args){
		$idItem = filter_var($args['idItem'], FILTER_SANITIZE_STRING);
		$tokenListe = filter_var($args['token'], FILTER_SANITIZE_STRING);
		$item = $this->recuperItem($request, $response, $idItem, $tokenListe);
		$liste = $item->liste;
        return $this->view->render($response, "affichageItem.html", compact("item","liste"));
	}

	public function reserverItem($request, $response, $args){
		$idItem = filter_var($args['idItem'], FILTER_SANITIZE_STRING);
		$tokenListe = filter_var($args['token'], FILTER_SANITIZE_STRING);
		$item = $this->recuperItem($request, $response, $idItem, $tokenListe);

		if($item->reserverPar == NULL){
			$item->reserverPar =  filter_var($request->getParsedBodyParam("nom", null), FILTER_SANITIZE_STRING);
			$item->save();
		}

		global $app;
		return $response->withRedirect($app->getContainer()->get('router')->pathFor("listeParticipantDetails", ["token" => $tokenListe]));
	}

	private function recuperItem($request, $response, $idItem, $tokenListe){
		$item = Item::find($idItem);
		$liste = Liste::where('tokenParticipant', '=', $tokenListe)->first();
		if($item->liste_id !== $liste->id){
			throw new \Slim\Exception\NotFoundException($request, $response);
		}
		return $item;
	}
}