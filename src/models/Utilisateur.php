<?php

namespace mywishlist\models;

class Utilisateur extends \illuminate\Database\Eloquent\Model{

	protected $table = 'user';
	protected $primaryKey = "id";
	protected $fillable = ['id' , "pseudo", "prenom", "nom", "mdp", "updated_at", "created_at"];

	public function listesCrees()
	{
		return $this->hasMany(Liste::class, "user_id");
	}

	/*
	public function delete()
	{
		foreach($this->listesCrees as $liste)
			$liste->delete();

		parent::delete();
	}*/

}
