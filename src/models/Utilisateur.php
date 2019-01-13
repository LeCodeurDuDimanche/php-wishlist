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

	public function listeParticipation()
	{
		 return $this->hasManyThrough(Liste::class, Item::class, "reserveParUser", "id", "id", "liste_id");
	}

	public function itemsReserves()
	{
		 return $this->hasMany(Item::class, "reserveParUser");
	}

	public function messages()
	{
		return $this->hasMany(MessagesListe::class, "user_id");
	}


	public function delete()
	{
		foreach($this->listesCrees as $liste)
			$liste->delete();

		foreach($this->messages as $message)
			$message->delete();

		foreach($this->itemsReserves as $item)
			$item->reserver(null);

		parent::delete();
	}

}
