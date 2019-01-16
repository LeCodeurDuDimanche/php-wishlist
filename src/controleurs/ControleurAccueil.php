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

    	$numPage = 1;
    	if(isset($args['numPage']) && $args['numPage'] !== null){
    		$numPage = intval($args['numPage']);
    	}

        $maxPage = intval(1 + (Liste::where("estPublique", "=", "1")->count() - 1) / $nbParPage);

        $numPage = $numPage > $maxPage ? $maxPage : $numPage;

    	$listes = Liste::where("estPublique", "=", "1")->take($nbParPage)->skip(($numPage-1)*$nbParPage)->get()
            ->reject(function ($e) {return  $e->estExpiree();})->sortBy("expiration")
            ->filter(function ($e) {return  $e->estValidee();});

    	return $this->view->render($response, "listesPubliques.html", compact("listes", "numPage", "maxPage"));
    }
}
