<?php

namespace mywishlist\models;

class MessagesListe extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'messagesListe';
	protected $primaryKey = 'id';

    protected $fillable = ['id' , "texte", "createur", "user_id", "liste_id", "updated_at", "created_at"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
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

}
