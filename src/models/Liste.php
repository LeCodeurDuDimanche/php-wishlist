<?php

namespace mywishlist\models;

class Liste extends BaseModel{

	protected $table = 'liste';
	protected $primaryKey = "id";

	protected $fillable = ['id' , "user_id", "createur", "titre", "desc", "expiration", "tokenCreateur", "tokenParticipant"," updated_at", "created_at", "estValidee", "estPublique"];


	public function items()
	{
		return $this->hasMany(Item::class);
	}

	public function createur() : string
	{
		if ($this->user_id)
		{
			$user = Utilisateur::where("id", "=", $this->user_id)->first();
			return $user ? $user->prenom . " " . $user->nom : "";
		}
		return $this->createur;
	}

	public function estValidee() : bool
	{
		if($this->estValidee == null){
			return false;
		}

		return $this->estValidee;
	}

	public function estExpiree() : bool
	{
		$date = $this->expiration instanceof \DateTime ? $this->expiration : date_create($this->expiration);
		return $date->getTimestamp() <= time();
	}

	public function messages()
	{
		return $this->hasMany(MessagesListe::class);
	}

	protected function doDelete() : bool
	{
		foreach($this->items as $item)
		{
			if (!$item->delete())
				return false;
		}
		foreach($this->messages as $message)
		{
			if (!$message->delete())
				return false;
		}

		return true;
	}

}
