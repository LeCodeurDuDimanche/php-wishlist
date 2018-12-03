<?php

namespace mywishlist\models;

class Utilisateur extends \illuminate\Database\Eloquent\Model{

	protected $table = 'clients';
	protected $primaryKey = "no";
	public $timestamps = false;
}
