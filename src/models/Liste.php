<?php

namespace mywishlist\models;

class Liste extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'liste';
	protected $primaryKey = "id";

	protected $fillable = ['id' , "user_id", "createur", "titre", "desc", "expiration", "tokenCreateur", "tokenParticipant"," updated_at", "created_at"];


	public function items()
	{
		return $this->hasMany(Item::class);
	}

	public function createur() : string
	{
		if ($this->user_id)
		{
			$user = Utilisateur::where("id", "=", $this->user_id)->first();
			return $user->prenom . " " . $user->nom;
		}
		return $this->createur;
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

}
