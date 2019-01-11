<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Item;
 use mywishlist\models\Liste;

class ControleurItem extends Controleur{

	public function afficherFormulaireReservation($request, $response, $args){
		$item = $this->recuperItem($request, $response, $args);

		if($item->reservePar != null){
			return Utils::redirect($response, "listeParticipantDetails", ["token" => $args['token']]);
		}

		return $this->view->render($response, "reserverItem.html", compact("item"));
	}

	public function afficherItem($request, $response, $args){
		$item = $this->recuperItem($request, $response, $args);
		$liste = $item->liste;
        return $this->view->render($response, "affichageItem.html", compact("item","liste"));
	}

	public function reserverItem($request, $response, $args){
		$item = $this->recuperItem($request, $response, $args);

        $nom = Utils::getFilteredPost($request, "nom");
        $message = Utils::getFilteredPost($request, "message");

        if ($nom === null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
        }
        else if ($item->reservePar != null)
        {
            Flash::flash("erreur", "L'item est déjà reservé par $item->reservePar.");
        }
        else {

			$item->reservePar = $nom;
            if ($message !== null)
                $item->message = $message;
			$item->save();
			$_SESSION['nomReservation'] = $item->reservePar;

            Flash::flash("message", "Réservation effectuée");
		}

		return Utils::redirect($response, "listeParticipantDetails", ["token" => $args['token']]);
	}

	private function recuperItem($request, $response, $args){
        //Mieux vaut un intval pour un int
		$idItem = intval($args['idItem']);
        //Pas besoin de sanitize le token
		$tokenListe = $args['token'];
		$item = Item::find($idItem);
		$liste = Liste::where('tokenParticipant', '=', $tokenListe)->first();
		if($item->liste_id !== $liste->id){
			throw new \Slim\Exception\NotFoundException($request, $response);
		}
		return $item;
	}
}
