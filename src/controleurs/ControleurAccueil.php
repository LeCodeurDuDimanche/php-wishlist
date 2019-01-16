<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Liste;
  use mywishlist\models\Utilisateur;
 use Illuminate\Support\Carbon;
 use Illuminate\Support\Collection;

 class ControleurAccueil extends Controleur{

    public function afficherAccueil($request, $response, $args)
    {
        return $this->view->render($response, "accueil.html");
    }

    public function afficherListesPubliques($request, $response, $args){
    	$nbParPage = 5;

        $recherche = $request->getQueryParam("q", null);

    	$numPage = 1;
    	if(isset($args['numPage']) && $args['numPage'] !== null){
    		$numPage = intval($args['numPage']);
    	}

    	$listes = Liste::where("estPublique", "=", "1")->where("estValidee", "=", "1")->where("expiration", ">", new Carbon());
        if ($recherche)
        {
            $listes = $listes->where(function($query) use ($recherche) {
                return $query->where("titre", "like", "%$recherche%")->orWhere("desc", "like", "%$recherche%");
            });
        }

        $maxPage = intval(1 + ($listes->count() - 1) / $nbParPage);
        $numPage = $numPage > $maxPage ? $maxPage : $numPage;

        $listes = $listes->orderBy("expiration")->take($nbParPage)->skip(($numPage - 1) * $nbParPage)->get();

        if ($recherche)
        {
            $listes = $listes->map(function($it) use ($recherche){
                $balise = "<span class='resultat-recherche'>$recherche</span>";
                $it->titre = \str_replace($recherche, $balise, $it->titre);
                $it->desc = \str_replace($recherche, $balise, $it->desc);
                return $it;
            });
        }

    	return $this->view->render($response, "listesPubliques.html", compact("recherche", "listes", "numPage", "maxPage"));
    }

    public function afficherCreateurs($request, $response, $args){
    	$nbParPage = 5;

        $recherche = $request->getQueryParam("q", null);

    	$numPage = 1;
    	if(isset($args['numPage']) && $args['numPage'] !== null){
    		$numPage = intval($args['numPage']);
    	}

    	$createurs = Liste::where("estPublique", "=", "1")->where("estValidee", "=", "1")->where("expiration", ">", new Carbon())->join("user", "user.id", "user_id");

        if ($recherche)
        {
            $createurs = $createurs->where(function($query) use ($recherche) {
                return $query->where("nom", "like", "%$recherche%")->orWhere("prenom", "like", "%$recherche%")->orWhere("pseudo", "like", "%$recherche%");
            });
        }


        $maxPage = intval(1 + ($createurs->count() - 1) / $nbParPage);

        $numPage = $numPage > $maxPage ? $maxPage : $numPage;

        $createurs = $createurs->orderBy("nom")->take($nbParPage)->skip(($numPage - 1) * $nbParPage);

        $createurs = $createurs->get()->map(function($l){ return Utilisateur::find($l->id);})->unique()->values();

        if ($recherche)
        {
            $createurs = $createurs->map(function($it) use ($recherche){
                $balise = "<span class='resultat-recherche'>$recherche</span>";
                $it->nom = \str_replace($recherche, $balise, $it->nom);
                $it->prenom = \str_replace($recherche, $balise, $it->prenom);
                $it->pseudo = \str_replace($recherche, $balise, $it->pseudo);
                return $it;
            });
        }

    	return $this->view->render($response, "createurs.html", compact("recherche", "createurs", "numPage", "maxPage"));
    }

}
