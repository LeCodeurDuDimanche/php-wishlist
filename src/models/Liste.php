<?php

namespace mywishlist\models;

class Liste extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'liste';
	protected $primaryKey = "id";

	protected $fillable = ['id' , "user_id", "titre", "desc", "expiration", "tokenCreateur", "tokenParticipant"," updated_at", "created_at"];


	public function items()
	{
		return $this->hasMany("mywishlist\models\Item", "liste_id");
	}

}
