<?php

namespace mywishlist\models;

class Cagnotte extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'cagnotte';
	protected $primaryKey = 'id';

    protected $fillable = ['id' , "item_id", "user_id", "createur", "montant"];

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
		return $this->createur;
	}

	public function auteur()
	{
		return Utilisateur::find($this->user_id);
	}

}
