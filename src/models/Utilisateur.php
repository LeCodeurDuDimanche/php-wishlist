<?php

namespace mywishlist\models;

class Utilisateur extends \illuminate\Database\Eloquent\Model{

	protected $table = 'user';
	protected $primaryKey = "id";
	protected $fillable = ['id' , "nom", "mdp", "updated_at", "created_at"];

}
