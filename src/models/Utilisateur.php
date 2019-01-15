<?php

namespace mywishlist\models;

class Utilisateur extends BaseModel{

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


	protected function doDelete() : bool
	{
		foreach($this->listesCrees as $liste)
		{
			if (!$liste->delete())
				return false;
		}

		foreach($this->messages as $message)
		{
			if (!$message->delete())
				return false;
		}

		foreach($this->itemsReserves as $item)
		{
			if (!$item->reserver(null))
				return false;
		}

		return parent::delete();
	}

}
