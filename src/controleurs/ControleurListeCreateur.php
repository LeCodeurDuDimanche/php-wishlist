<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

 use mywishlist\models\Item;

 class ControleurListeCreateur extends Controleur{


     private function generateToken() : string
     {
         //TODO: check for uniqueness
         return strtr(base64_encode(random_bytes(24)), "+/", "-_");
     }

     private function isValidDate(string $date) : bool
     {
         try{
             new \DateTime($date);
         } catch(Exception $e)
         {
             return false;
         }
         return true;
     }

     public function afficherFormulaireCreation($request, $response, $args)
     {
         return $this->view->render($response, "createur/creerListe.html");
     }

     public function creerListe($request, $response, $args)
     {
         $nom = $request->getParsedBodyParam("nom", null);
         $desc = $request->getParsedBodyParam("description", null);
         $expiration = $request->getParsedBodyParam("expiration", null);

         if ($nom == null || $desc == null || $expiration == null ||
            !$this->isValidDate($expiration))
        {
            throw new Exception("Données invalides");
        }

        $nom = filter_var($nom, FILTER_SANITIZE_STRING);
        $desc = filter_var($nom, FILTER_SANITIZE_STRING);
        $expiration = new \DateTime($expiration);

        $liste = new Liste();
        $liste->titre = $nom;
        $liste->desc = $desc;
        $liste->expiration = $expiration;
        $liste->tokenCreateur = $this->generateToken();
        $liste->tokenParticipant = $this->generateToken();

        $liste->save();

        global $app;
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("listeCreateur", ["id" => $liste->tokenCreateur]));
     }


     public function afficherFormulaireAjoutItem($request, $response, $args)
     {
        $tokenCreateur = filter_var($args['id'], FILTER_SANITIZE_STRING);
  		$liste = Liste::where("tokenCreateur", "=", $tokenCreateur)->first();
        return $this->view->render($response, "createur/ajouterItem.html", compact("liste"));
     }

     public function ajouterItem($request, $response, $args)
     {
        $titre = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
        $descrip = filter_var($_POST["desc"], FILTER_SANITIZE_STRING);
        $img = filter_var($_POST["image"], FILTER_SANITIZE_STRING);
        $url = filter_var($_POST["url"], FILTER_SANITIZE_STRING);
        $prix = filter_var($_POST["tarif"], FILTER_SANITIZE_STRING);
        $token = filter_var($args['id'],FILTER_SANITIZE_STRING );
        $item = new Item();
        $item->titre = $titre;
        $item->desc = $descrip;
        $item->img = $img;
        $item->url = $url;
        $item->tarif = $prix;
        $liste = Liste::where("tokenCreateur", "=", $token)->first();
        $item->liste_id = $liste->id;
        $item->save();
        global $app;
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("listeCreateurDetails", ["id" => $token]));
     }

     public function modifierItem($request, $response, $args)
     {
        $titre = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
        $descrip = filter_var($_POST["desc"], FILTER_SANITIZE_STRING);
        $img = filter_var($_POST["image"], FILTER_SANITIZE_STRING);
        $url = filter_var($_POST["url"], FILTER_SANITIZE_STRING);
        $prix = filter_var($_POST["tarif"], FILTER_SANITIZE_STRING);
        $token = filter_var($args['id'],FILTER_SANITIZE_STRING );
        $item = Item::where('id', '=', $args['num'])->first();
        $item->titre = $titre;
        $item->desc = $descrip;
        $item->img = $img;
        $item->url = $url;
        $item->tarif = $prix;
        $item->save();
        global $app;
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("listeCreateurDetails", ["id" => $token]));
     }

	 public function afficherModifItemListe($request, $response, $args)
     {
  		$liste = Liste::where('tokenCreateur', '=', $args['id'])->first();
        $item = Item::where('id', '=', $args['num'])->first();
        return $this->view->render($response, "createur/modifierItem.html", compact("liste", "item"));
     }

 	public function afficherListe($request, $response, $args){
 		$liste =Liste::where('tokenCreateur', '=', $args['id'])->first();
 		return $this->view->render($response, "createur/affichageListe.html", compact("liste"));
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
 		$liste =Liste::where('tokenCreateur', '=', $args['id'])->first();
        if($liste == null){
            throw new \Slim\Exception\NotFoundException($request, $response);  
        }
 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "createur/affichageListeDetails.html", compact("liste", "listeIt"));
 	}

 }
