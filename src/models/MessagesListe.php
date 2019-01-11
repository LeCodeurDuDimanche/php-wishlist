<?php

namespace mywishlist\models;

class MessagesListe extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'messagesListe';
	protected $primaryKey = 'id';

    protected $fillable = ['id' , "texte", "createur", "liste_id", "updated_at", "created_at"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
	}
}
