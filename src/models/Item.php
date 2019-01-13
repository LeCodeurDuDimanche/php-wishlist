<?php

namespace mywishlist\models;

class Item extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'item';
	protected $primaryKey = 'id';


    protected $fillable = ['id' , "liste_id", "titre", "desc", "url", "tarif", "img","reservePar","updated_at", "created_at"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
	}



	public function delete()
	{
		//Supprimer image si uploadée			
		parent::delete();
	}
}
