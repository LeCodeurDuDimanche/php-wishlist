<?php

namespace mywishlist\models;

class Cagnotte extends BaseModel{

	protected $table = 'cagnotte';
	protected $primaryKey = 'id';

    protected $fillable = ['id' , "item_id", "user_id", "nom", "montant"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
	}

	public function createur() : string
	{
		if ($this->user_id)
		{
			$user = Utilisateur::find($this->user_id);
			return $user ? $user->prenom . " " . $user->nom : "";
		}
		return $this->nom;
	}

	public function auteur()
	{
		return Utilisateur::find($this->user_id);
	}


	protected function doDelete() :bool {
		return true;
	}

}
