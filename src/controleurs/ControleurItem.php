<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Item;
 use mywishlist\models\Liste;
 use mywishlist\models\Cagnotte;

class ControleurItem extends Controleur{

	public function afficherFormulaireReservation($request, $response, $args){
		$item = $this->recuperItem($request, $response, $args);

		if($item->estReserve() && !$item->aCagnotte){
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
        else if ($item->estReserve())
        {
            Flash::flash("erreur", "L'item est déjà reservé par " . $item->reservePar());
        }
        else {
            //On reserve par user si connecte, par nom sinon
            $user = Authentification::getUtilisateur();
			$item->reserver($user ? $user : $nom);

            if ($message !== null)
                $item->message = $message;

			$item->save();
			$_SESSION['nomReservation'] = $item->reservePar();

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

	public function participerItem($request, $response, $args)
	{
		$item = $this->recuperItem($request, $response, $args);
        $nom = Utils::getFilteredPost($request, "nom");
        $cagnotteParticipant = Utils::getFilteredPost($request, "cagnotte");

        if ($nom === null)
        {
            Flash::flash("erreur", "Des données sont manquantes");
        }

        $lcagnottes = $item->cagnottes()->get();
        //var_dump($lcagnottes);
        $sum = 0;
        foreach ($lcagnottes as $key => $value) {
        	$sum += $value->montant;
        }
        $cagnotteMax = $item->tarif - $sum;
        if($cagnotteParticipant > $cagnotteMax)
        {
        	Flash::flash("erreur", "D'autres personnes ont déjà participé, vous pouvez participer à une hauteur de ". $cagnotteMax . "€");
        	return Utils::redirect($response, "formulaireReserverItem", ["token" => $args['token'], "idItem" => $args['idItem']]);
        }
        else {
            $insertCagnotte = new Cagnotte();
            $insertCagnotte->item_id = $item->id;
            $user = Authentification::getUtilisateur();
            if($user === null){
                $insertCagnotte->nom = $nom;
                $insertCagnotte->user_id = null;
            }
            else {
                $insertCagnotte->nom = null;
                $insertCagnotte->user_id = $user->id;
            }
            $insertCagnotte->montant = $cagnotteParticipant;
            $insertCagnotte->save();
        	return Utils::redirect($response, "listeParticipantDetails", ["token" => $args['token']]);
        }


	}
}
