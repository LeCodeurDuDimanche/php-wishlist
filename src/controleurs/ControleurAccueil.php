<?php
namespace mywishlist\controleurs;

 use mywishlist\models\Liste;

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

        $maxPage = intval(1 + (Liste::where("estPublique", "=", "1")->count() - 1) / $nbParPage);

        $numPage = $numPage > $maxPage ? $maxPage : $numPage;

    	$listes = Liste::where("estPublique", "=", "1")->where("estValidee", "=", "1")->where("expiration", "<", time());
        if ($recherche)
        {
            $listes = $listes->where("titre", "like", "%$recherche%")->orWhere("desc", "like", "%$recherche%");
        }
        $listes = $listes->orderBy("expiration")->take($nbParPage)->skip(($numPage - 1) * $nbParPage)->get();


    	return $this->view->render($response, "listesPubliques.html", compact("recherche", "listes", "numPage", "maxPage"));
    }
}
