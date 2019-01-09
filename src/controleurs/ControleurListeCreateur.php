<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use mywishlist\models\Item;
 use Slim\Exception\NotFoundException;

 class ControleurListeCreateur extends Controleur{


     private function generateToken() : string
     {
         do{
             $token = strtr(base64_encode(random_bytes(24)), "+/", "-_");
         } while (Liste::where("tokenCreateur", "=", $token)->orWhere("tokenParticipant", "=", $token)->count());
         return $token;
     }

     private static function recupererListe($request, $response, $args)
     {
        $tokenCreateur = $args['id'];
   		$liste = Liste::where("tokenCreateur", "=", $tokenCreateur)->first();
        if ($liste === null)
            throw new NotFoundException($request, $response);
        return $liste;
     }

     public function afficherFormulaireCreation($request, $response, $args)
     {
         return $this->view->render($response, "createur/creerListe.html");
     }

     public function creerListe($request, $response, $args)
     {
         $nom = Utils::getFilteredPost($request, "nom");
         $desc = Utils::getFilteredPost($request, "description");
         $expiration = $request->getParsedBodyParam("expiration", null);
         $user_id = $request->getParsedBodyParam("userId", null);
         $createur = Utils::getFilteredPost($request, "createur");

         if ($nom == null || $desc == null || $expiration == null || ($createur == null && $user_id == null) ||
            !Utils::isValidDate($expiration))
        {
            throw new Exception("Données invalides");
        }

        $expiration = new \DateTime($expiration);

        $liste = new Liste();
        $liste->titre = $nom;
        $liste->desc = $desc;
        $liste->expiration = $expiration;
        $liste->user_id = $user_id === null ? null : intval($user_id);
        $liste->createur = $createur;
        $liste->tokenCreateur = $this->generateToken();
        $liste->tokenParticipant = $this->generateToken();

        $liste->save();

        //ajout d'un cokkie qui a une duree de vie de 2 mois après l'expiration
        setcookie("liste".$liste->id, $liste->tokenCreateur, $liste->expiration->getTimestamp() + 3600*24*60);

        return Utils::redirect($response, "listeCreateur", ["id" => $liste->tokenCreateur]);
     }

     public function modifierListe($request, $response, $args)
     {
         $nom = Utils::getFilteredPost($request, "nom");
         $desc = Utils::getFilteredPost($request, "description");
         $conf = Utils::getFilteredPost($request, "confidentialite");
         $expiration = $request->getParsedBodyParam("expiration", null);

         if ($nom == null || $desc == null || $conf == null || $expiration == null ||
            !Utils::isValidDate($expiration) || !in_array($conf, ["publique", "privee"]))
        {
            throw new Exception("Données invalides");
        }

        $expiration = new \DateTime($expiration);

        $liste = self::recupererListe($request, $response, $args);
        $liste->titre = $nom;
        $liste->desc = $desc;
        $liste->expiration = $expiration;
        $liste->estPublique = $conf === "publique";

        if (! $liste->save())
            Flash::flash("erreur", "Impossible de modifier la liste");
        else
            Flash::flash("message", "Modification réussie");

        return Utils::redirect($response, "listeCreateur", ["id" => $liste->tokenCreateur]);
     }

     public function afficherFormulaireModification($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args);

        return $this->view->render($response, "createur/affichageFormulaireModification.html", compact("liste"));
     }

     public function afficherFormulaireAjoutItem($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args);

        return $this->view->render($response, "createur/ajouterItem.html", compact("liste"));
     }

     public function ajouterItem($request, $response, $args)
     {
        global $app;

        $titre = Utils::getFilteredPost($request, "nom");
        $descrip = Utils::getFilteredPost($request, "desc");
        $url = Utils::getFilteredPost($request, "url");
        $img = Utils::getFilteredPost($request, "img");
        $prix = Utils::getFilteredPost($request, "tarif");
        //pas besoin de filtrer le token
        $token = $args['id'];

        $files = $request->getUploadedFiles();
        $file = isset($files["fichierImg"]) ? $files["fichierImg"] : null;
        if ($titre && $descrip && $prix && (($file && !$file->getError()) || $img))
        {
            if ($file && !$file->getError())
            {
                $ext = pathinfo($files["fichierImg"]->getClientFilename(), PATHINFO_EXTENSION);
                $filename = strtr(base64_encode(random_bytes(24)), "+/", "-_") . "." . $ext;
                //Check nom unique
                $relativeFilename = "ressources/uploaded/$filename";
                $fullFilename = $app->getContainer()->rootDir. "/$relativeFilename";

                $file->moveTo($fullFilename);

                $filename = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $request->getUri()->getBasePath() . "/" . $relativeFilename;
            }
            else
                $filename = $img;

            $item = new Item();
            $item->titre = $titre;
            $item->desc = $descrip;
            $item->img = $filename;
            $item->url = $url;
            $item->tarif = $prix;
            $liste = Liste::where("tokenCreateur", "=", $token)->first();
            $item->liste_id = $liste->id;
            $item->save();
            return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);

        } else {
            Flash::flash("erreur", "Des données sont manquantes ou invalides");
            return Utils::redirect($response, "formulaireAjouterItem", ["id" => $args['id']]);
        }
    }

     public function modifierItem($request, $response, $args)
     {
         $titre = Utils::getFilteredPost($request, "nom");
         $descrip = Utils::getFilteredPost($request, "desc");
         $url = Utils::getFilteredPost($request, "url");
         $img = Utils::getFilteredPost($request, "image");
         $prix = Utils::getFilteredPost($request, "tarif");
        $token = $args['id'];

        if ($titre && $descrip && $url && $img && $prix)
        {
            $item = Item::where('id', '=', intval($args['num']))->first();
            if ($item === null)
                throw new NotFoundException($request, $response);

            $item->titre = $titre;
            $item->desc = $descrip;
            $item->img = $img;
            $item->url = $url;
            $item->tarif = $prix;
            $item->save();
            return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
        } else {
            Flash::flash("erreur", "Des données sont manquantes ou invalides");
            return Utils::redirect($response, "formulaireModificationItem", ["id" => $args['id']]);
        }

     }

     public function supprimerItem($request, $response, $args){
        $numItem = intval($args['num']);
        $item = Item::where('id', '=', $numItem)->first();
        $token = $args['id'];
        $item->delete();
        return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
     }

	 public function afficherModifItemListe($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args);

        $item = Item::where('id', '=', $args['num'])->first();
        return $this->view->render($response, "createur/modifierItem.html", compact("liste", "item"));
     }

 	public function afficherListe($request, $response, $args){
        $liste = self::recupererListe($request, $response, $args);

 		return $this->view->render($response, "createur/affichageListe.html", compact("liste"));
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
        $liste = self::recupererListe($request, $response, $args);

 		$listeIt = $liste->items()->get();
 		return $this->view->render($response, "createur/affichageListeDetails.html", compact("liste", "listeIt"));
 	}

    public function afficherMesListes($request, $response, $args){
        if(Authentification::getUtilisateur()->estConnecte()){
            $meslistes = Authentification::mesListes()->get();
            return $this->view->render($response, "createur/affichageMesListes.html", compact("meslistes"));
        } else {
            
        }
    }

 }
