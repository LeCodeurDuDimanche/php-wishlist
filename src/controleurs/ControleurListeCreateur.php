<?php

 namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
 use mywishlist\models\Item;
 use Slim\Exception\NotFoundException;
 use Illuminate\Support\Collection;
 use Psr\Http\Message\ServerRequestInterface;
 use Psr\Http\Message\ResponseInterface;

 class ControleurListeCreateur extends Controleur{


     private function generateToken() : string
     {
         do{
             $token = strtr(base64_encode(random_bytes(24)), "+/", "-_");
         } while (Liste::where("tokenCreateur", "=", $token)->orWhere("tokenParticipant", "=", $token)->count());
         return $token;
     }

     private static function recupererListe($request, $response, $tokenCreateur)
     {
   		$liste = Liste::where("tokenCreateur", "=", $tokenCreateur)->first();
        if ($liste === null)
            throw new NotFoundException($request, $response);
        return $liste;
    }

    private static function checkType($file, $type = 'all')
    {
       $finfo = finfo_open(FILEINFO_MIME_TYPE);
       if (!$finfo)
           return false;

       $mime = finfo_file($finfo, $file);

       finfo_close($finfo);
       var_dump($mime);
       if (!$mime)
           return false;

       if ($type !== "all")
       {
           if ($type === 'url')
           {
               return strpos($mime, "image/") === 0;
           }
           else
               return false;
       }
       return true;
    }

     private static function checkUrl($url)
     {
         if (strpos($url, "http://") !== 0 && strpos($url, "https://") !== 0)
             return false;

        $curl = curl_init();
        if (!$curl)
            return false;

        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP|CURLPROTO_HTTPS);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);

        //Head request
        curl_setopt($curl, CURLOPT_HEADER, true); // header will be at output
        curl_setopt($curl, CURLOPT_NOBODY, true);

        $content = curl_exec($curl);
        $headers = explode("\n", $content);
        $state = count($headers) ? $headers[0] : "";
        $code = intval(substr($state, 9, 3));

        curl_close($curl);

        return $code >= 200 && $code < 400;
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
         $confidentialite = Utils::getFilteredPost($request, "confidentialite");

         if ($nom == null || $desc == null || $expiration == null || $confidentialite == null || ($createur == null && $user_id == null))
        {
            Flash::flash("erreur", "Des données sont manquantes");
            return Utils::redirect($response, "afficherFormulaireCreation");
        }
        else if (!Utils::isValidDate($expiration))
        {
            Flash::flash("erreur", "La date d'expiration a un format invalide. Il doit être AAAA-MM-JJ");
            return Utils::redirect($response, "afficherFormulaireCreation");
        }

        $expiration = new \DateTime($expiration);

        $liste = new Liste();
        $liste->titre = $nom;
        $liste->desc = $desc;
        $liste->expiration = $expiration;
        $liste->estPublique = $confidentialite === "publique";
        $liste->user_id = $user_id === null ? null : intval($user_id);
        $liste->createur = $createur;
        $liste->tokenCreateur = $this->generateToken();
        $liste->tokenParticipant = $this->generateToken();

        $liste->save();

        //ajout d'un cookie qui a une duree de vie de 2 mois après l'expiration
        Utils::setListeCookie($liste);

        return Utils::redirect($response, "listeCreateur", ["id" => $liste->tokenCreateur]);
     }

     public function modifierListe($request, $response, $args)
     {
         $nom = Utils::getFilteredPost($request, "nom");
         $desc = Utils::getFilteredPost($request, "description");
         $conf = Utils::getFilteredPost($request, "confidentialite");
         $expiration = $request->getParsedBodyParam("expiration", null);

         if ($nom == null || $desc == null || $conf == null || $expiration == null ||
            !Utils::isValidDate($expiration) || !in_array($conf, ["publique", "privée"]))
        {
            Flash::flash("erreur", "Des données sont manquantes ou invalides");
        }
        else {
            $expiration = new \DateTime($expiration);

            $liste = self::recupererListe($request, $response, $args['id']);
            $liste->titre = $nom;
            $liste->desc = $desc;
            $liste->expiration = $expiration;
            $liste->estPublique = $conf === "publique";

            if (! $liste->save())
                Flash::flash("erreur", "Impossible de modifier la liste");
            else {
                Flash::flash("message", "Modification réussie");

                //Mise à jour du cookie de liste (possible changement de la date d'expiration)
                Utils::setListeCookie($liste);
            }
        }

        return Utils::redirect($response, "listeCreateur", ["id" => $args['id']]);
     }

     public function supprimerListe($request, $response, $args){
        $liste = self::recupererListe($request, $response, $args['id']);
        $liste->delete();
        Flash::flash("message", "Suppression réussie");
        return $response;
     }



     public function afficherFormulaireModification($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args['id']);

        return $this->view->render($response, "createur/affichageFormulaireModification.html", compact("liste"));
     }

     public function afficherFormulaireAjoutItem($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args['id']);

        return $this->view->render($response, "createur/ajouterItem.html", compact("liste"));
     }

     public function imageUpload($request, $file)
     {
         global $app;

         if ($file->getSize() > 2 * 1024 * 1024)
        {
            Flash::flash("erreur", "L'image doit faire moins de 2 Mo");
            return false;
        }
        else if (!self::checkType($file->file, "img"))
        {
            Flash::flash("erreur", "Le fichier doit être une image valide");
            return false;
        }

         $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
         $filename = strtr(base64_encode(random_bytes(24)), "+/", "-_") . "." . $ext;
         //Check nom unique
         $relativeFilename = "ressources/uploaded/$filename";
         $fullFilename = $app->getContainer()->rootDir. "/$relativeFilename";

         $file->moveTo($fullFilename);

         return "/" .$request->getUri()->getBasePath() . "/" . $relativeFilename;
     }

     public function ajouterItem($request, $response, $args)
     {
        $token = $args['id'];
        $liste = self::recupererListe($request, $response, $token);

        $titre = Utils::getFilteredPost($request, "nom");
        $descrip = Utils::getFilteredPost($request, "desc");
        $url = filter_var(Utils::getFilteredPost($request, "url"), FILTER_SANITIZE_URL);
        $img = filter_var(Utils::getFilteredPost($request, "img"), FILTER_SANITIZE_URL);
        $prix = Utils::getFilteredPost($request, "tarif");
        $choixImage = Utils::getFilteredPost($request, "choixImage");
        $checkboxCagnotte = $_POST['cagnotte'];


        if($checkboxCagnotte === "on")
            $checkboxCagnotte = true;
        else
            $checkboxCagnotte = false;
        $cagnotte = new Cagnotte();

        $files = $request->getUploadedFiles();
        $file = isset($files["fichierImg"]) ? $files["fichierImg"] : null;
        if ($titre !== null && $descrip !== null && $prix !== null && $choixImage !== null &&
        (($choixImage == "Upload" && $file && !$file->getError()) || ($choixImage === "Url" && $img) || ($choixImage === "Aucun")))
        {
            if ($choixImage === "Upload")
            {
                $filename = $this->imageUpload($request, $files["fichierImg"]);
                if (!$filename)
                {
                    return Utils::redirect($response, "formulaireAjouterItem", ["id" => $token]);
                }
            }
            else if ($choixImage == "Url"){
                $filename = $img;
            }
            else {
                $filename = null;
            }

            if ($choixImage == "Url" && !self::checkUrl($filename))
            {
                Flash::flash("erreur", "Le lien de l'image est invalide");
            }
            else if ($url && !self::checkUrl($url)) {
                Flash::flash("erreur", "Le lien attaché à l'item est invalide");
            }
            else {
                $item = new Item();
                $item->titre = $titre;
                $item->desc = $descrip;
                if ($filename)
                    $item->img = $filename;
                $item->url = $url;
                $item->imgLocale = $choixImage === "Upload";
                $item->tarif = $prix;
                $item->aCagnotte = $cagnotte;
                $liste = Liste::where("tokenCreateur", "=", $token)->first();
                $item->liste_id = $liste->id;
                $item->save();

                Flash::flash("message", "Item ajouté");
                return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
            }
        } else {
            Flash::flash("erreur", "Des données sont manquantes ou invalides");
        }
        return Utils::redirect($response, "formulaireAjouterItem", ["id" => $args['id']]);

    }

    public function supprimerImage($request, $response, $args){
        $item = Item::where('id', '=', intval($args['num']))->first();
        $item->img = null;
        $item->save();

        $item->supprimerImage();

        Flash::flash("message", "Suppression réussie");
        return $response;
     }

     public function modifierItem($request, $response, $args)
     {
        $token = $args['id'];
        $liste = self::recupererListe($request, $response, $token);

        $titre = Utils::getFilteredPost($request, "nom");
        $descrip = Utils::getFilteredPost($request, "desc");
        $url = filter_var(Utils::getFilteredPost($request, "url"), FILTER_SANITIZE_URL);
        $img = filter_var(Utils::getFilteredPost($request, "img"), FILTER_SANITIZE_URL);
        $prix = Utils::getFilteredPost($request, "tarif");
        $choixImage = Utils::getFilteredPost($request, "choixImage");

        $files = $request->getUploadedFiles();
        $file = isset($files["fichierImg"]) ? $files["fichierImg"] : null;

        //var_dump($titre, $descrip, $prix, $choixImage, (($choixImage == "Upload" && $file && !$file->getError()) || ($choixImage === "Url" && $img) || $choixImage === "Aucun"));die();
        if ($titre !== null && $descrip !== null && $prix !== null && $choixImage !== null
         && (($choixImage == "Upload" && $file && !$file->getError()) || ($choixImage === "Url" && $img) || $choixImage === "Aucun"))
        {

            if ($choixImage === "Upload")
            {
                $filename = $this->imageUpload($request, $files["fichierImg"]);
                if (!$filename)
                    return Utils::redirect($response, "formulaireModifItem", ["id" => $args['id'], "num" => $args['num']]);

            }
            else if ($choixImage == "Url")
                $filename = $img;
            else
                $filename = null;

            if ($choixImage == "Url" && !self::checkUrl($filename))
            {
                Flash::flash("erreur", "Le lien de l'image est invalide");
            }
            else if ($url && !self::checkUrl($url)) {
                Flash::flash("erreur", "Le lien attaché à l'item est invalide");
            }
            else {
                $item = Item::where('id', '=', intval($args['num']))->first();
                if ($item === null)
                    throw new NotFoundException($request, $response);

                $item->titre = $titre;
                $item->desc = $descrip;
                if ($filename)
                {
                    if ($item->imgLocale)
                        $item->supprimerImage();

                    $item->img = $filename;
                    $item->imgLocale = $choixImage === "Upload";
                }
                $item->url = $url;
                $item->tarif = $prix;
                $item->save();
                Flash::flash("message", "Item modifié");
                return Utils::redirect($response, "listeCreateurDetails", ["id" => $liste->tokenCreateur]);
            }
        } else {
            Flash::flash("erreur", "Des données sont manquantes ou invalides");
        }
        return Utils::redirect($response, "formulaireModifItem", ["id" => $args['id'], "num" => $args['num']]);
     }

     public function supprimerItem($request, $response, $args){
        $numItem = intval($args['num']);
        $item = Item::where('id', '=', $numItem)->first();
        $token = $args['id'];
        $item->delete();
        Flash::flash("message", "Item supprimé");
        return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
     }

	 public function afficherModifItemListe($request, $response, $args)
     {
        $liste = self::recupererListe($request, $response, $args['id']);

        $item = Item::where('id', '=', $args['num'])->first();
        return $this->view->render($response, "createur/modifierItem.html", compact("liste", "item"));
     }

 	public function afficherListe($request, $response, $args){
        $liste = self::recupererListe($request, $response, $args['id']);

 		return $this->view->render($response, "createur/affichageListe.html", compact("liste"));
 	}

 	public function afficherListeAvecDetails($request, $response, $args){
        $liste = self::recupererListe($request, $response, $args['id']);

 		$listeIt = $liste->items()->get();

        $nbContrib = $listeIt->keyBy('reserverParUser')->count() + $listeIt->keyBy('reservePar')->count() - 2;
        $nbItems = $listeIt->count();
        $prixTotal = $listeIt->sum('tarif');
        $nbItemsReserves = $listeIt->filter(function($it) {return $it->estReserve();})->count();


        $valeursStats = [
                ["val" => $nbContrib, "text" => "$nbContrib contributeurs", "bg" => "success"],
                ["val" => $nbItems, "text" => $nbItems . ($nbItems > 1 ? " items" : " item"), "bg" => "warning"],
                ["val" => $prixTotal / 20, "text" => "Prix total de $prixTotal €", "bg" => "info"]
            ];
 		return $this->view->render($response, "createur/affichageListeDetails.html", compact("liste", "listeIt", "valeursStats", "nbItemsReserves"));
 	}

    public function afficherMesListes($request, $response, $args){

        if($user = Authentification::getUtilisateur()){
            $meslistes = $user->listesCrees;
            $listeParticipation = $user->listeParticipation;
        }
        else {
            $meslistes = new Collection();
            $listeParticipation = new Collection();
        }

        $idListesCookies = Utils::getValidListesCookie();
        foreach($idListesCookies as $id)
            $meslistes->push(Liste::find($id));

        $meslistes->sortByDesc("created_at");

        $meslistes = $meslistes->all();


        return $this->view->render($response, "affichageMesListes.html", compact("meslistes"));
    }

    /**
    * Permet de filtrer les non createurs de la liste
    */
    public static function checkCreateurMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        global $app;
        $route = $request->getAttribute('route');
        $token = $route->getArgument('id');
        $liste = self::recupererListe($request, $response, $token);
        $estCreateur = false;

        //Check user_id et connecter
        if ($liste->user_id)
        {
            if (Authentification::getIdUtilisateur() != $liste->user_id)
            {
                if (Authentification::estConnecte())
                    $data = ["erreur" => "Cette liste ne vous appartient pas, vous pouvez y participer si le créateur vous donne le lien de partage. Connectez-vous au compte avec lequel vous avez créé cette liste si c'est la vôtre."];
                else
                    $data = ["avertissement" => "Cette liste appartient à un utilisateur. Connectez-vous pour y accéder, si la liste vous appartient."];

                return $app->getContainer()->view->render($response, "createur/affichageListeErreur.html", $data);
            }
        }
        else
        {
            //Sinon check cookies
            if (!in_array($liste->id, Utils::getValidListesCookie()))
            {
                //Si une requete GET avec le nom a été envoyé, on check le nom
                if ($request->getQueryParam("createur", null) === $liste->createur)
                {
                    //Si on est bien le créateur, on remet le cookie
                    Utils::setListeCookie($liste);
                }
                else {
                    return $app->getContainer()->view->render($response, "createur/affichageListeErreur.html", ["form" => true, "avertissement"=> "Nous n'avons pas réussi à vous authentifier, veuillez indiquer votre nom pour prouver que c'est bien votre liste"]);
                }
            }
        }

        return $next($request, $response);
    }

    /**
    * Permet d'interdire la modification d'une liste expirée
    */
    public static function checkNonPerimeeMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        $route = $request->getAttribute('route');
        $token = $route->getArgument('id');
        $liste = self::recupererListe($request, $response, $token);
        //On ne peut pas modifier une liste expiree
        if ($liste->estExpiree())
        {
            Flash::flash("erreur", "Impossible de modifier une liste expirée");
            return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
        }

        return $next($request, $response);

    }

    /**
    * Permet d'interdire la modification d'un item reserve
    */
    public static function checkNonReserveMiddleware(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        $route = $request->getAttribute('route');
        $token = $route->getArgument('id');
        $liste = self::recupererListe($request, $response, $token);

        $numItem = intval($route->getArgument('num'));
        $item = Item::where('id', '=', $numItem)->first();

        //On ne peut pas modifier un item reserve
        if ($item->estReserve())
        {
            Flash::flash("erreur", "Impossible de modifier ou de supprimer un item reservé");
            return Utils::redirect($response, "listeCreateurDetails", ["id" => $token]);
        }

        return $next($request, $response);

    }

 }
